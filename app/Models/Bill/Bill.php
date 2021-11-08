<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Models\Transaction\Transaction;
use App\Models\Wallet\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                     $id
 * @property Type                    $type
 * @property Status                  $status
 * @property int                     $wallet_from_id
 * @property Wallet                  $wallet_from
 * @property int                     $wallet_to_id
 * @property Wallet                  $wallet_to
 * @property Collection<Transaction> $transactions
 * @property ?Error                  $error
 * @property string                  $value
 */
class Bill extends Model
{
    use HasFactory;

    public const MIN_TRANSFER = '0.00001';

    public const MAX_TRANSFER = '9.99';

    /**
     * 0.05 = 5 процентов
     */
    public const TRANSFER_COMMISSION = '0.05';

    protected $table = 'bills';

    protected $fillable = ['type_id', 'status_id', 'wallet_from_id', 'wallet_to_id', 'value'];

    /**
     * Возвращает тип счет
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    /**
     * Возвращает статус счета
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'type_id');
    }

    /**
     * Возвращает кошелек отправителя
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet_from()
    {
        return $this->belongsTo(Wallet::class, 'wallet_from_id', 'id');
    }

    /**
     * Возвращает кошелек получателя
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet_to()
    {
        return $this->belongsTo(Wallet::class, 'wallet_to_id', 'id');
    }

    public function error()
    {
        return $this->hasOne(Error::class, 'bill_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bill_id', 'id');
    }
}
