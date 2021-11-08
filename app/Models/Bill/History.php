<?php

declare(strict_types=1);

namespace App\Models\Bill;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $action
 * @property string $payload
 */
class History extends Model
{
    use HasFactory;

    protected $table = 'bills_history';

    protected $fillable = ['bill_id', 'action', 'payload'];

    protected $casts = [
        'payload' => AsCollection::class
    ];
}
