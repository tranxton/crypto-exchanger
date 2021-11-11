<?php

declare(strict_types=1);

namespace App\Jobs\Referral;

use App\Models\Referral\Charge;
use App\Models\Referral\ChargeStatus;
use App\Models\User\User;
use App\Models\Wallet\SystemWallet;
use App\Models\Wallet\Wallet;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChargeBonuses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $system_wallets;

    public function __construct()
    {
        $this->system_wallets = SystemWallet::all();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var Collection<Charge> $charges
         */
        $charges = Charge::where('status_id', ChargeStatus::CREATED)->take(100)->get();
        if ($charges->count() === 0) {
            Log::info("No charges to complete. Exit");
            return;
        }

        $this->completeCharges($charges);
    }

    /**
     * Завершает начисление бонус по реферальной программе
     *
     * @param Collection $charges
     */
    private function completeCharges(Collection $charges): void
    {
        /**
         * @var Charge $charge
         */
        foreach ($charges as $charge) {
            DB::beginTransaction();
            try {
                $this->completeCharge($charge);
            } catch (Exception $e) {
                DB::rollBack();

                $charge->fail();
                Log::error("Charge didn't complete: {$e->getMessage()}", ['charge' => $charge->toJson()]);

                continue;
            }
            DB::commit();
        }
    }

    private function completeCharge(Charge $charge)
    {
        $user = $charge->user;
        /**
         * @var Wallet $system_wallet
         */
        $system_wallet = $this->system_wallets->where('currency_id', $charge->currency_id)->first();
        /**
         * @var Wallet $wallet
         */
        $wallet = $user->wallets->where('currency_id', $charge->currency_id)->first();
        if ($wallet === null) {
            throw new Exception("Charge for user skipped: no wallet");
        }

        $system_wallet->decreaseBalance($charge->value);
        $system_wallet->save();

        $wallet->increaseBalance($charge->value);
        $wallet->save();

        $charge->complete();

        $this->dropCaches($user, $wallet);
    }

    /**
     * Сбрасывает кэши
     *
     * @param User   $user
     * @param Wallet $wallet
     */
    private function dropCaches(User $user, Wallet $wallet): void
    {
        $this->dropUserCache($user);
        $this->dropWalletCache($wallet);
    }

    /**
     * Сбрасывает кэш пользователя
     *
     * @param User $user
     */
    private function dropUserCache(User $user): void
    {
        UserRepository::cacheUser($user);
    }

    /**
     * Сбрасывает кэш кошелька
     *
     * @param Wallet $wallet
     */
    private function dropWalletCache(Wallet $wallet): void
    {
        WalletRepository::cacheWallet($wallet);
    }
}
