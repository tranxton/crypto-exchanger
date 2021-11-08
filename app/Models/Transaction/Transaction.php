<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use App\Models\Bill\Bill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int    $id
 * @property Bill   $bill
 * @property Type   $type
 * @property Status $status
 * @property Error  $error
 * @property string $value
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['bill_id', 'status_id', 'type_id', 'value'];

    /**
     * Возвращает счет, которому принадлежит транзакция
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    /**
     * Возвращает тип транзакции
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    /**
     * Возвращает статус транзакции
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    /**
     * Возвращает ошибку транзакции
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function error()
    {
        return $this->hasOne(Error::class, 'transaction_id', 'id');
    }
}
