<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransferTransactionCompleted;
use App\Helpers\BCMathHelper;
use App\Models\Bill\Bill;
use App\Models\Bill\BillTransfer;
use App\Models\Referral\Charge;
use App\Models\Referral\ChargeStatus;
use App\Models\Referral\Level;
use App\Models\Transaction\Transaction;
use App\Models\User\Referral;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChargeBonusToUsers
{
    use BCMathHelper;

    /**
     * @var \Illuminate\Database\Eloquent\Collection<Level>
     */
    private $levels;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->levels = Level::all();
    }

    /**
     * Handle the event.
     *
     * @param TransferTransactionCompleted $event
     *
     * @return void
     */
    public function handle(TransferTransactionCompleted $event)
    {
        try {
            $this->chargeBonuses($event->bill);
        } catch (Exception $e) {
            Log::error("Bonuses were not charged: {$e->getMessage()}", ['bill' => $event->bill->toJson()]);
        }
    }

    /**
     * @param Bill $bill
     *
     * @throws Exception
     */
    private function chargeBonuses(Bill $bill): void
    {
        /**
         * @var Collection<Referral> $users
         */
        $users = Referral::select(['user_id', 'level_id'])->where('referral_id', $bill->user->id)->get();
        if ($users->count() === 0) {
            throw new Exception('No referrals');
        }

        $commission = BillTransfer::getCommission($bill);
        if ($commission === null) {
            throw new Exception("Could not find bill commission");
        }

        /**
         * @var Referral $user
         */
        $new_charges = $this->createNewCharges($users, $bill, $commission);

        if (!Charge::insert($new_charges->toArray())) {
            throw new Exception("Could not insert charges");
        }
    }

    /**
     * Создает список новых начислений
     *
     * @param Collection  $users
     * @param Bill        $bill
     * @param Transaction $commission
     *
     * @return Collection
     */
    private function createNewCharges(Collection $users, Bill $bill, Transaction $commission): Collection
    {
        $new_charges = new Collection();
        foreach ($users as $user) {
            $new_charges->add(
                [
                    'bill_id'     => $bill->id,
                    'user_id'     => $user->user_id,
                    'currency_id' => $bill->sender_wallet->currency->id,
                    'referral_id' => $bill->user->id,
                    'level_id'    => $user->level_id,
                    'status_id'   => ChargeStatus::CREATED,
                    'value'       => $this->getChargeValue($commission->value, $user->level_id),
                ]
            );
        }

        return $new_charges;
    }

    /**
     * Находит сумму для начисления рефералу
     *
     * @param string $value
     * @param int    $level_id
     *
     * @return string
     */
    private function getChargeValue(string $value, int $level_id): string
    {
        $percent = $this->levels->where('id', $level_id)->first()->percent;

        return self::multiplication($value, self::division($percent, '100'));
    }
}
