<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User\User;
use App\Models\Wallet\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        /**
         * @var User $this
         */
        return [
            'name'      => $this->name,
            'email'     => $this->email,
            'wallets'   => $this->getWallets($this->wallets),
            'referrals' => [
                'amount' => $this->referrals->count(),
            ],
        ];
    }

    private function getWallets(Collection $wallets): array
    {
        $result = [];

        /**
         * @var Wallet $wallet
         */
        foreach ($wallets as $wallet) {
            $result[] = [
                'address'  => $wallet->address,
                'value'    => $wallet->value,
                'currency' => [
                    'full_name'  => $wallet->currency->full_name,
                    'short_name' => $wallet->currency->short_name,
                ],
            ];
        }

        return $result;
    }
}
