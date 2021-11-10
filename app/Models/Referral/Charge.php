<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int          $id
 * @property int          $bill_id
 * @property int          $user_id
 * @property int          $level_id
 * @property ChargeStatus $status
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
}
