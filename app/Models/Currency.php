<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $full_name
 * @property string $short_name
 * @property int    $address_length
 */
class Currency extends Model
{
    use HasFactory;
}
