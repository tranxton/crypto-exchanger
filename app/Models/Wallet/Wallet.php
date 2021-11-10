<?php

declare(strict_types=1);

namespace App\Models\Wallet;

use App\Helpers\BCMathHelper;
use App\Models\Bill\Bill;
use App\Models\Currency;
use App\Models\User\User;
use App\Repositories\BillRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int              $id
 * @property User             $user
 * @property Currency         $currency
 * @property Collection<Bill> $bills
 * @property Type             $type
 * @property string           $address
 * @property string           $value
 */
class Wallet extends Model
{
    use HasFactory, BCMathHelper;

    public const DEFAULT_VALUE = '0.00';

    protected $fillable = ['user_id', 'type_id', 'currency_id', 'address', 'value'];

    /**
     * Возвращает владельца кошелька
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Возвращает валюту кошелька
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'sender_wallet_id', 'id');
    }

    /**
     * Возвращает тип кошелька
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    /**
     * Проверяет является ли пользователь владельце кошелька
     *
     * @param User $user
     *
     * @return bool
     */
    public function isUserOwner(User $user): bool
    {
        return $this->user->id === $user->id;
    }

    /**
     * Возвращает текущий баланс кошелька
     *
     * @return string
     */
    public function getBalance(): string
    {
        /**
         * @var Collection<Bill> $active_bills
         */
        $active_bills = BillRepository::getActive($this->user)->where('wallet_id', $this->id)->collect();
        if ($active_bills->count() === 0) {
            return $this->value;
        }

        $active_bills_sum = $active_bills->reduce(
            function ($carry, $item) {
                return self::addition($carry, $item->value);
            },
            '0.00'
        );

        return self::subtraction($this->value, $active_bills_sum);
    }

    /**
     * Увеличивает баланс кошелька
     *
     * @param string $value
     *
     * @return string
     */
    public function increaseBalance(string $value): string
    {
        $this->value = self::addition($this->value, $value);

        return $this->value;
    }

    /**
     * Уменьшает баланс кошелька
     *
     * @param string $value
     *
     * @return string
     */
    public function decreaseBalance(string $value): string
    {
        $this->value = self::subtraction($this->value, $value);

        return $this->value;
    }
}
