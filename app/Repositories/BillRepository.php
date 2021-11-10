<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Bill\Bill;
use App\Models\Bill\BillTransfer;
use App\Models\Bill\Status;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BillRepository
{
    /**
     * Префикс кэша для счета
     *
     * @var string
     */
    public static string $cache_prefix = 'bill_';

    /**
     * Префикс кэша для всех счетов пользователя
     *
     * @var string
     */
    public static string $cache_all_prefix = 'user_bills_';

    /**
     * Длительность жизни кэша для одного счета
     *
     * @var int
     */
    public static int $cache_ttl = 900;

    /**
     * Длительность жизни кэша для всех счетов
     *
     * @var int
     */
    protected static int $cache_all_ttl = 3600;

    /**
     * Получить счет
     *
     * @param int $id
     *
     * @return Bill
     */
    public static function get(int $id): Bill
    {
        $cache_key = self::$cache_prefix . $id;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $bill = Bill::find($id);
        self::cacheBill($bill);

        return $bill;
    }

    /**
     * Получить все счета пользователя
     *
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all(User $user): Collection
    {
        $cache_key = self::$cache_all_prefix . $user->name;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $bills = $user->bills;
        Cache::put($cache_key, $bills, self::$cache_all_ttl);

        return $bills;
    }

    /**
     * Возвращает список активных счетов пользователя
     *
     * @param User $user
     *
     * @return Collection
     */
    public static function getActive(User $user): Collection
    {
        $bills = self::all($user);

        return $bills->where('status', Status::ACTIVE)->collect();
    }

    /**
     * Создает запрос на перевод
     *
     * @param User   $user
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     * @param string $value
     *
     * @return Bill
     * @throws \Exception
     */
    public static function create(User $user, Wallet $sender_wallet, Wallet $recipient_wallet, string $value): Bill
    {
        $bill = BillTransfer::create($user, $sender_wallet, $recipient_wallet, $value);
        self::cacheBill($bill);
        self::dropCacheUserBills($user);

        return $bill;
    }

    /**
     * Подтверждает запрос на перевод
     *
     * @param Bill $bill
     *
     * @return Bill
     * @throws Exception
     */
    public static function accept(Bill $bill): Bill
    {
        $bill->status_id = Status::ACCEPTED;
        if (!$bill->save()) {
            throw new Exception('Не удалось изменить статус счета', 500);
        }
        self::cacheBill($bill);

        return $bill;
    }

    /**
     * Помещает счет в кэш
     *
     * @param Bill $bill
     *
     * @return bool
     */
    public static function cacheBill(Bill $bill): bool
    {
        $bill->load('status');

        return Cache::put(self::$cache_prefix . $bill->id, $bill, self::$cache_ttl);
    }

    /**
     * Сбрасывает кэш для для всех счетов пользователя
     *
     * @param User $user
     *
     * @return bool
     */
    public static function dropCacheUserBills(User $user): bool
    {
        return Cache::forget(self::$cache_all_prefix . $user->name);
    }
}
