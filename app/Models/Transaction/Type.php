<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 */
class Type extends Model
{
    use HasFactory;

    public const TRANSFER = 1;

    public const COMMISSION = 2;

    public const BONUS = 3;

    protected $table = 'transaction_types';
}
