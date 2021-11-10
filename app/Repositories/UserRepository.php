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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Префикс кэша для пользователя
     *
     * @var string
     */
    public static string $cache_prefix = 'user_';

    /**
     * Время жизни кэша
     *
     * @var int
     */
    public static int $cache_ttl = 3600;

    /**
     * Возвращает пользователя по имени
     *
     * @param User $name
     *
     * @return mixed
     * @throws Exception
     */
    public static function getByName(string $name): User
    {
        $cache_key = self::$cache_prefix . $name;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        /**
         * @var User $user
         */
        $user = User::where('name', $name)->with(['wallets', 'referrals'])->first();
        if ($user->isSystem()) {
            throw new Exception('Нельзя получить системный аккаунт');
        }

        self::cacheUser($user);

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
                $referred_link = LinkRepository::get($referral_link);
                if ($referred_link === null) {
                    DB::rollBack();

                    throw new Exception('Недействительная реферальная ссылка', 422);
                }

                if (!self::linkReferralToUser($referred_link->user, $referral)) {
                    throw new Exception('Не удалось привязать реферала к пользователям', 500);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            $context = [
                'error'     => $e->getMessage(),
                'user'      => $referral ?? null,
                'user_link' => $link ?? null,
            ];
            Log::error("Can't create user", $context);

            throw new Exception($e->getMessage(), 500, $e);
        }
        DB::commit();

        self::cacheUser($referral);

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

        if (!Referral::insert($relationships->toArray())) {
            return false;
        }

        self::dropUsersCache($relationships->pluck('user_id')->all());

        return true;
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
        $cache_key = self::$cache_prefix . $user->name;
        $user->api_token = Str::random(60);
        if (!$user->save()) {
            throw new Exception('Не удалось авторизовать пользователя', 500);
        }

        Cache::put($cache_key, $user, self::$cache_ttl);

        return $user;
    }



    /**
     * Помещает пользователя в кэш
     *
     * @param User $user
     *
     * @return bool
     */
    public static function cacheUser(User $user): bool
    {
        $user->load(['wallets', 'referrals']);

        return Cache::put(self::$cache_prefix . $user->id, $user, self::$cache_ttl);
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

    /**
     * Сбрасывает кэш у списка пользователей
     *
     * @param array $ids
     */
    private static function dropUsersCache(array $ids): void
    {
        /**
         * @var Collection<User> $users
         */
        $users = User::select('name')->whereIn('id', $ids)->get();

        /**
         * @var User $user
         */
        $users->each(function ($user, $key) {
            Cache::forget(self::$cache_prefix . $user->name);
        });
    }
}
