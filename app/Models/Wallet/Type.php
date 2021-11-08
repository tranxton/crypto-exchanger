<?php

declare(strict_types=1);

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 */
class Type extends Model
{
    use HasFactory;

    protected $table = 'wallet_types';

    public const SYSTEM = 1;

    public const USER = 1;
}
