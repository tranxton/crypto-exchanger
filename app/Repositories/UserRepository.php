<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Referral\Level;
use App\Models\Referral\Link;
use App\Models\User\Referral;
use App\Models\User\Type;
use App\Models\User\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Создание пользователя в системе
     *
     * @param string      $name
     * @param string      $email
     * @param string      $password
     * @param string|null $referral_link
     *
     * @return User
     * @throws Exception
     */
    public static function create(string $name, string $email, string $password, ?string $referral_link = null): User
    {
        DB::beginTransaction();
        try {
            /**
             * @var User $referral
             */
            $user_data = [
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
                'type_id'  => Type::USER,
            ];
            $referral = User::create($user_data);

            $link = Link::create(['user_id' => $referral->id, 'value' => Str::random(5)]);
            $referral->referral_link()->save($link);

            if (isset($referral_link)) {
                $user = Link::getOwner($referral_link);
                if ($user === null) {
                    DB::rollBack();

                    throw new Exception('Недействительная реферальная ссылка');
                }

                self::linkReferralToUser($user, $referral);
            }
        } catch (Exception $e) {
            DB::rollBack();

            $context = [
                'error'                => $e->getMessage(),
                'user'                 => $referral ??= null,
                'user_link'            => $link ??= null,
            ];
            Log::error("Can't create user", $context);

            throw new Exception($e->getMessage(), 0, $e);
        }
        DB::commit();

        return $referral;
    }

    /**
     * Привязывает реферала к пользователю и его "владельцам"
     *
     * @param User $user
     * @param User $referral
     *
     * @return bool
     */
    public static function linkReferralToUser(User $user, User $referral): bool
    {
        $users = User::where('id', $user->id)->with('allOwners')->get();
        $relationships = self::createReferralToUsersRelationship($users, $referral, Level::MIN);

        return Referral::insert($relationships->toArray());
    }

    /**
     * Рекурсивно привязывает реферала к пользователям
     *
     * @param Collection $users
     * @param User       $referral
     * @param int        $level
     *
     * @return Collection
     */
    private static function createReferralToUsersRelationship(Collection $users, User $referral, int $level): Collection
    {
        /**
         * @var User $user
         */
        $collection = new Collection();
        foreach ($users as $user) {
            $collection->add(['user_id' => $user->id, 'referral_id' => $referral->id, 'level_id' => $level++]);
            if ($level === Level::MAX) {
                return $collection;
            }
            if ($user->allOwners->count() !== 0) {
                $new_collection = self::createReferralToUsersRelationship($user->allOwners, $referral, $level);
                $collection = $collection->merge($new_collection);
            }
        }

        return $collection;
    }
}
