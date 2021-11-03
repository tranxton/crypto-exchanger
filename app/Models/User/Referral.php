<?php

declare(strict_types=1);

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $referral_id
 * @property int $level_id
 */
class Referral extends Model
{
    use HasFactory;

    protected $table = 'users_referrals';

    protected $fillable = ['user_id', 'referral_id', 'level_id'];
}
