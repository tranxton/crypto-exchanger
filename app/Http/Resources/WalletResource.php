<?php

namespace App\Http\Resources;

use App\Models\Wallet\Wallet;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /**
         * @var Wallet $this
         */
        $wallet = [
            'address'  => $this->address,
            'value'    => $this->getBalance(),
            'currency' => new CurrencyResource($this->currency),
        ];

        return $wallet;
    }
}
