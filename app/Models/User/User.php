<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\Referral\Link;
use App\Models\Wallet\Wallet;
use Exception;
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
        'type_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function referrals()
    {
        return $this->belongsToMany(User::class, Referral::class, 'user_id', 'referral_id');
    }

    /**
     * Возвращает список владельцев реферала
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function owners()
    {
        return $this->belongsToMany(User::class, Referral::class, 'referral_id', 'user_id');
    }

    /**
     * Возвращает список владельцев и их владельцев (рекурсивно)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allOwners()
    {
        return $this->owners()->with('allOwners');
    }/**
 * Возвращает пользователя
 *
 * @param int $id
 *
 * @return User
 * @throws Exception
 */
    public static function get(int $id): User
    {
        /**
         * @var User $user
         */
        $user = User::find($id);
        if ($user->type->id === Type::SYSTEM) {
            throw new Exception('Нельзя получить системный аккаунт');
        }

        return $user;
    }

    /**
     * Возвращает пользователя по имени
     *
     * @param string $name
     *
     * @return User
     * @throws Exception
     */
    public static function getByName(string $name): User
    {
        /**
         * @var User $user
         */
        $user = User::where('name', $name)->first();
        if ($user->type->id === Type::SYSTEM) {
            throw new Exception('Нельзя получить системный аккаунт');
        }

        return $user;
    }
}
