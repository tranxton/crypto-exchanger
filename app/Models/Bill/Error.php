<?php

declare(strict_types=1);

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $message
 */
class Error extends Model
{
    use HasFactory;

    protected $table = 'bill_errors';
}
