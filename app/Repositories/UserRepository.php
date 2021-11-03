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
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Возвращает пользователя
     *
     * @param int $id
     *
     * @return User
     * @throws Exception
     */
    public static function get(int $id): User
    {
        /**
         * @var User $user
         */
        $user = User::find($id);
        if ($user->type->id === Type::SYSTEM) {
            throw new Exception('Нельзя получить системный аккаунт');
        }

        return $user;
    }

    /**
     * Возвращает пользователя по имени
     *
     * @param string $name
     *
     * @return User
     * @throws Exception
     */
    public static function getByName(string $name): User
    {
        /**
         * @var User $user
         */
        $user = User::where('name', $name)->first();
        if ($user->type->id === Type::SYSTEM) {
            throw new Exception('Нельзя получить системный аккаунт');
        }

        return $user;
    }

    /**
     * Создание пользователя в системе
     *
     * @param string      $name
     * @param string      $email
     * @param string      $password
     * @param string|null $referral_link
     *
     * @return User
     */
    public static function create(array $fields): User
    {
        $fields['password'] = Hash::make($fields['password']);

        DB::beginTransaction();
        try {
            /**
             * @var User $referral
             */
            $referral = User::create($fields);

            $referral_link = Link::create(['user_id' => $referral->id, 'value' => Str::random(5)]);
            $referral->referral_link()->save($referral_link);

            if (isset($fields['referral_link'])) {
                $user = self::getLinkOwner($fields['referral_link']);
                self::linkReferralToUser($user, $referral);
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception('Не удалось зарегистрировать пользователя', 0, $e);
        }
        DB::commit();

        return $referral;
    }

    /**
     * Авторизует пользователя в системе
     *
     * @param User $user
     *
     * @return User
     * @throws Exception
     */
    public static function login(User $user): User
    {
        $user->api_token = Str::random(60);
        if (!$user->save()) {
            throw new \Exception('Не удалось сохранить авторизационный токен');
        }

        return $user;
    }

    /**
     * Возвращает владельца реферальной ссылки
     *
     * @param string $link
     *
     * @return User
     */
    public static function getLinkOwner(string $link): User
    {
        return Link::where('value', $link)->with('user')->first()->user;
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
