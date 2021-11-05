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

    public const BITCOIN = 1;

    public const ETHEREUM = 2;

    /**
     * Получить валюту по сокращенному имени
     *
     * @param string $short_name
     *
     * @return static|null
     */
    public static function getByShortName(string $short_name): ?self
    {
        return self::where('short_name', $short_name)->first();
    }
}
