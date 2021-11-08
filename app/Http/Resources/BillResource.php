<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Bill\Bill;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="Bill",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="status", type="string"),
     *           @OA\Property(property="type", type="string"),
     *           @OA\Property(property="value", type="string"),
     *           @OA\Property(property="wallet_sender", type="string"),
     *           @OA\Property(property="wallet_recipient", type="string"),
     *           @OA\Property(property="transactions", type="array",
     *              @OA\Items(ref="#/components/schemas/Transaction")
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
    public function toArray($request)
    {
        /**
         * @var Bill $this
         */
        return [
            'id'               => $this->id,
            'type'             => $this->type->name,
            'status'           => $this->status->name,
            'value'            => $this->value,
            'wallet_sender'    => $this->wallet_from->address,
            'wallet_recipient' => $this->wallet_to->address,
            'transactions'     => TransactionResource::collection($this->transactions),
        ];
    }
}
