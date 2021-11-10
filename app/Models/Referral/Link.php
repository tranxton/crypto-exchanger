<?php

declare(strict_types=1);

namespace App\Models\Referral;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property User   $user
 * @property string $value
 */
class Link extends Model
{
    use HasFactory;

    protected $table = 'referral_links';

    protected $fillable = ['user_id', 'value'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
