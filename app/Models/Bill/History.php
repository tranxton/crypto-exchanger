<?php

declare(strict_types=1);

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $action
 * @property string $payload
 * @property string $payload_new
 */
class History extends Model
{
    use HasFactory;

    protected $table = 'bill_history';
}
