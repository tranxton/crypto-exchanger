<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Helpers\BCMathHelper;
use App\Models\Bill\Bill;
use App\Models\Bill\Status;
use App\Models\Bill\Type;
use App\Models\Wallet\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillRepository
{
    use BCMathHelper;

    /**
     * Создание запроса на перевод между кошельками
     *
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     * @param string $value
     *
     * @return Bill
     * @throws Exception
     */
    public static function createTransfer(Wallet $sender_wallet, Wallet $recipient_wallet, string $value): Bill
    {
        DB::beginTransaction();
        try {
            /**
             * @var Bill $bill
             */
            $bill_data = [
                'type_id'        => Type::TRANSFER,
                'status_id'      => Status::CREATED,
                'wallet_from_id' => $sender_wallet->id,
                'wallet_to_id'   => $recipient_wallet->id,
                'value'          => $value,
            ];
            $bill = Bill::create($bill_data);

            $commission_value = self::calculateCommission($bill->value);
            $transaction_commission = TransactionRepository::createCommission($bill, $commission_value);

            $transfer_value = self::subtraction($bill->value, $commission_value);
            $transaction_transfer = TransactionRepository::createTransfer($bill, $transfer_value);
        } catch (Exception $e) {
            DB::rollBack();

            $context = [
                'error'                  => $e->getMessage(),
                'bill'                   => $bill ??= null,
                'transaction_commission' => $transaction_commission ??= null,
                'transaction_transfer'   => $transaction_transfer ??= null,
            ];
            Log::error("Can't create bill", $context);

            throw new Exception('Не удалось создать перевод', 0, $e);
        }

        DB::commit();

        return $bill;
    }

    /**
     * Возвращает список активных платежи
     *
     * @param Wallet $wallet
     *
     * @return Collection<?Bill>
     */
    public static function getActive(Wallet $wallet): Collection
    {
        return Bill::where('wallet_from_id', $wallet->id)->whereIn('status_id', Status::ACTIVE)->get();
    }

    /**
     * Рассчитывает сумму комиссии для переданного числа
     *
     * @param string $value
     *
     * @return string
     */
    private static function calculateCommission(string $value): string
    {
        return self::multiplication($value, Bill::TRANSFER_COMMISSION);
    }
}
