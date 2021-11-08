<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Crypto Exchanger API Documentation",
     *      description="Crypto Exchanger API Documentation",
     *      @OA\Contact(
     *          email="rustem.akhmetzianov@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="API Server"
     * )
     *
     * @OA\Tag(
     *     name="Пользователь",
     *     description="Работа пользователем в системе"
     * )
     * @OA\Tag(
     *     name="Кошелек",
     *     description="Работа с кошельком в системе"
     * )
     * @OA\Tag(
     *     name="Перевод",
     *     description="Работа с переводами/транзакциями в системе"
     * )
     * @OA\Tag(
     *     name="Валюта",
     *     description="Работа с валютами в системе"
     * )
     *
     * @OA\Schema(
     *   schema="Wallet",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="address", type="string"),
     *           @OA\Property(property="value", type="string"),
     *           @OA\Property(property="currency", type="object", ref="#/components/schemas/Currency")
     *       )
     *   }
     * )
     *
     * @OA\Schema(
     *   schema="Referral",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="amount", type="integer")
     *       )
     *   }
     * )
     *
     * @OA\Schema(
     *   schema="Auth",
     *   type="object",
     *   allOf={
     *       @OA\Schema(
     *           @OA\Property(property="token", type="string")
     *       )
     *   }
     * )
     */
}
