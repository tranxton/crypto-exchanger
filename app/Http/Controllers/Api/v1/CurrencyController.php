<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    /**
     * @OA\Get(
     *      path="/currency/all",
     *      operationId="getList",
     *      tags={"Валюта"},
     *      summary="Получить список доступных валют",
     *      description="Возвращает список объектов Currency",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Токен",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Currency"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Требуется авторизация",
     *      )
     * )
     *
     * Возвращает список доступных валют
     *
     * @return Response
     */
    public function getList(): Response
    {
        return new Response(CurrencyResource::collection(Currency::all()));
    }
}
