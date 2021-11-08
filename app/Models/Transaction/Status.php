<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 */
class Status extends Model
{
    use HasFactory;

    public const BLOCKED = 1;

    public const COMPLETED = 2;

    public const FAILED = 3;

    public const EXPIRED = 4;

    protected $table = 'transaction_statuses';
}
