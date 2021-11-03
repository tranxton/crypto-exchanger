<?php

declare(strict_types=1);

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class Type extends Model
{
    use HasFactory;

    public const SYSTEM = 1;

    public const USER = 2;

    protected $table = 'user_types';
}
