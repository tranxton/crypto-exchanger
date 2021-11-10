<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * @OA\Schema(
     *   schema="Referral",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="name", type="string")
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
         * @var User $this
         */
        return [
            'name'  => $this->name
        ];
    }
}
