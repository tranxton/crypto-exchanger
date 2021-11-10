<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeStatus extends Model
{
    use HasFactory;

    public const CREATED = 1;

    public const CHARGED = 2;

    public const FAILED = 3;

    protected $table = 'referral_charge_statuses';
}
