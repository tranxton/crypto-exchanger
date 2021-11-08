<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $table = 'transaction_history';

    protected $fillable = ['transaction_id', 'action', 'payload'];

    protected $casts = [
        'payload' => AsCollection::class
    ];
}
