<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User\User;
use App\Models\Wallet\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="User",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="name", type="string"),
     *           @OA\Property(property="email", type="string"),
     *           @OA\Property(property="wallets", type="array",
     *              @OA\Items(ref="#/components/schemas/Wallet")
     *           ),
     *           @OA\Property(property="referrals", type="array",
     *              @OA\Items(ref="#/components/schemas/Referral")
     *           )
     *       )
     *   }
     * )
     *
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
            'wallets'   => WalletResource::collection($this->wallets),
            'referrals' => ReferralResource::collection($this->referrals),
        ];
    }
}
