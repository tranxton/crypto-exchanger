<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Models\Transaction\Transaction;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                     $id
 * @property Type                    $type
 * @property User                    $user
 * @property Status                  $status
 * @property int                     $status_id
 * @property int                     $sender_wallet_id
 * @property Wallet                  $sender_wallet
 * @property int                     $recipient_wallet_id
 * @property Wallet                  $recipient_wallet
 * @property Collection<Transaction> $transactions
 * @property ?Error                  $error
 * @property string                  $value
 * @property string                  $expires_at
 */
class Bill extends Model
{
    use HasFactory;

    /**
     * Минимальная сумма перевода
     */
    public const MIN_TRANSFER = '0.00001';

    /**
     * Максимальная сумма перевода
     */
    public const MAX_TRANSFER = '9.99';

    /**
     * Время жизни счета в минутах
     */
    public const EXPIRES_IN = 15;

    protected $table = 'bills';

    protected $fillable = [
        'user_id',
        'type_id',
        'status_id',
        'sender_wallet_id',
        'recipient_wallet_id',
        'value',
        'expires_at',
    ];

    /**
     * Возвращает тип счета
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    /**
     * Возвращает владельца счета
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Возвращает статус счета
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    /**
     * Возвращает кошелек отправителя
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender_wallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id', 'id');
    }

    /**
     * Возвращает кошелек получателя
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient_wallet()
    {
        return $this->belongsTo(Wallet::class, 'recipient_wallet_id', 'id');
    }

    public function error()
    {
        return $this->hasOne(Error::class, 'bill_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bill_id', 'id');
    }

    /**
     * Проверяет не истекло ли время жизни счета
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return (new \DateTime())->format('Y-m-d H:i:s') >= $this->expires_at;
    }

    /**
     * Изменяет статус счета на "Завершено"
     *
     * @return bool
     */
    public function complete()
    {
        $this->status_id = Status::COMPLETED;

        return $this->save();
    }

    /**
     * Изменяет статус счета на "Подтверждена"
     *
     * @return bool
     */
    public function accept(): bool
    {
        $this->status_id = Status::ACCEPTED;

        return $this->save();
    }

    /**
     * Изменяет статус счета на "Просрочен"
     *
     * @return bool
     */
    public function expire()
    {
        $this->status_id = Status::EXPIRED;

        return $this->save();
    }

    /**
     * Изменяет статус счета на "Ошибка завершения"
     *
     * @return bool
     */
    public function fail()
    {
        $this->status_id = Status::FAILED;

        return $this->save();
    }

    /**
     * Является ли пользователь владельцем счета
     *
     * @param User $user
     *
     * @return bool
     */
    public function isUserOwner(User $user): bool
    {
        return $this->user->id === $user->id;
    }
}
