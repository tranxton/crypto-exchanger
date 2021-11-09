<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Helpers\BCMathHelper;
use App\Models\Transaction\TransactionCommission;
use App\Models\Transaction\TransactionTransfer;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillTransfer extends Bill
{
    use BCMathHelper;

    /**
     * 0.05 = 5 процентов
     */
    public const COMMISSION = '0.05';

    /**
     * Создание запроса на перевод между кошельками
     *
     * @param User   $user
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     * @param string $value
     *
     * @return Bill
     * @throws Exception
     */
    public static function create(User $user, Wallet $sender_wallet, Wallet $recipient_wallet, string $value): Bill
    {
        DB::beginTransaction();
        try {
            /**
             * @var Bill $bill
             */
            $expires_in = Bill::EXPIRES_IN;
            $expires_at = (new DateTime())->add(new DateInterval("PT{$expires_in}M"))->format('Y-m-d H:i:s');
            $bill_data = [
                'user_id'             => $user->id,
                'type_id'             => Type::TRANSFER,
                'status_id'           => Status::CREATED,
                'sender_wallet_id'    => $sender_wallet->id,
                'recipient_wallet_id' => $recipient_wallet->id,
                'value'               => $value,
                'expires_at'          => $expires_at,
            ];
            $bill = Bill::create($bill_data);

            $commission_value = self::calculateCommission($bill->value);
            $transaction_commission = TransactionCommission::create($bill, $commission_value);

            $transfer_value = self::subtraction($bill->value, $commission_value);
            $transaction_transfer = TransactionTransfer::create($bill, $transfer_value);
        } catch (Exception $e) {
            DB::rollBack();

            $context = [
                'error'                  => $e->getMessage(),
                'bill'                   => $bill ?? null,
                'transaction_commission' => $transaction_commission ?? null,
                'transaction_transfer'   => $transaction_transfer ?? null,
            ];
            Log::error("Can't create bill", $context);

            throw new Exception('Не удалось создать перевод', 500, $e);
        }

        DB::commit();

        return $bill;
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
        return self::multiplication($value, BillTransfer::COMMISSION);
    }
}
