<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\Bill\Bill;
use App\Models\Referral\Link;
use App\Models\Wallet\Wallet;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int                $id
 * @property string             $name
 * @property string             $email
 * @property Type               $type
 * @property string             $password
 * @property string             $api_token
 * @property Link               $referral_link
 * @property Collection<Wallet> $wallets
 * @property Collection<Bill>   $bills
 * @property Collection<User>   $referrals
 * @property Collection<User>   $owners
 * @property Collection<User>   $allOwners
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }


    /**
     * Возвращает список кошельков
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'user_id', 'id');
    }

    /**
     * Возвращает список счетов
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bills()
    {
        return $this->hasMany(Bill::class, 'user_id', 'id');
    }

    /**
     * Возвращает реферальной ссылку
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function referral_link()
    {
        return $this->hasOne(Link::class, 'user_id', 'id');
    }

    /**
     * Возвращает список рефералов пользователя
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function referrals()
    {
        return $this->hasManyThrough(User::class, Referral::class, 'user_id', 'id','id','referral_id');
    }

    /**
     * Возвращает список владельцев реферала
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function owners()
    {
        return $this->hasManyThrough(User::class, Referral::class, 'user_id', 'id','id','user_id');
    }

    /**
     * Возвращает список владельцев и их владельцев (рекурсивно)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allOwners()
    {
        return $this->belongsToMany(User::class, Referral::class, 'referral_id', 'user_id')->with('allOwners');
    }

    /**
     * Является ли аккаунт системным
     *
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->type->id === Type::SYSTEM;
    }
}
