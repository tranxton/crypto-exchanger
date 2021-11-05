<?php

namespace App\Http\Resources;

use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="Currency",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="full_name", type="string"),
     *           @OA\Property(property="short_name", type="string")
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
         * @var Currency $this
         */
        $currency = [
            'full_name'  => $this->full_name,
            'short_name' => $this->short_name,
        ];


        return $currency;
    }
}
