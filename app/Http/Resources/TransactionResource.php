<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Transaction\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="Transaction",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="status", type="string"),
     *           @OA\Property(property="type", type="string"),
     *           @OA\Property(property="value", type="string")
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
         * @var Transaction $this
         */
        return [
            'status' => $this->status->name,
            'type'   => $this->type->name,
            'value'  => $this->value,
        ];
    }
}
