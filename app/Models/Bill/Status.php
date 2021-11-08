<?php

declare(strict_types=1);

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 */
class Status extends Model
{
    use HasFactory;

    public const CREATED = 1;

    public const ACCEPTED = 2;

    public const COMPLETED = 3;

    public const FAILED = 4;

    public const EXPIRED = 5;

    public const ACTIVE = [self::CREATED, self::ACCEPTED];

    public const COMPLETED_SUCCESSFULLY = [self::COMPLETED];

    public const COMPLETED_WITH_ERRORS = [self::FAILED, self::EXPIRED];

    protected $table = 'bill_statuses';
}
