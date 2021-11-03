<?php

declare(strict_types=1);

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    public const MIN = 1;

    public const MAX = 10;

    protected $table = 'referral_levels';
}
