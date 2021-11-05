<?php

declare(strict_types=1);

namespace App\Models\Wallet;

use App\Models\Currency;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $id
 * @property User     $user
 * @property Currency $currency
 * @property Type     $type
 * @property string   $address
 * @property string   $value
 */
class Wallet extends Model
{
    use HasFactory;

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
     *
     *
     * @param string $address
     *
     * @return static
     */
    public static function getByAddress(string $address): self
    {
        return self::where('address', $address)->first();
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
}
