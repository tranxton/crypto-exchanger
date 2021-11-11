<?php

declare(strict_types=1);

namespace App\Models\Referral;

use App\Models\Bill\Bill;
use App\Models\Currency;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int          $id
 * @property Bill         $bill
 * @property int          $bill_id
 * @property Currency     $currency
 * @property int          $currency_id
 * @property User         $user
 * @property int          $user_id
 * @property int          $level_id
 * @property ChargeStatus $status
 * @property int          $status_id
 * @property string       $value
 */
class Charge extends Model
{
    use HasFactory;

    protected $table = 'referral_charges';

    protected $fillable = ['bill_id', 'user_id', 'referral_id', 'level_id', 'status_id', 'value'];

    public function status()
    {
        return $this->hasOne(ChargeStatus::class, 'status_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    /**
     * Переводит задачу с начислением бонус в статус "Начислены"
     *
     * @return bool
     */
    public function complete(): bool
    {
        $this->status_id = ChargeStatus::CHARGED;

        return $this->save();
    }

    /**
     * Переводит задачу с начислением бонус в статус "Завершена с ошибкой"
     *
     * @return bool
     */
    public function fail(): bool
    {
        $this->status_id = ChargeStatus::FAILED;

        return $this->save();
    }
}
