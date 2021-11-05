<?php

declare(strict_types=1);

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 */
class Type extends Model
{
    use HasFactory;

    protected $table = 'bill_types';

    /**
     * Перевод между кошельками
     */
    public const TRANSFER = 1;
}
