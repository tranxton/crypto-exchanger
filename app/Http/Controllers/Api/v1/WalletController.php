<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Wallet\CreateRequest as CreateWalletRequest;
use App\Http\Requests\Wallet\GetRequest as GetWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Currency;
use App\Models\Wallet\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/wallet/all",
     *      operationId="getList",
     *      tags={"Кошелек"},
     *      summary="Получение списка кошельков пользователя",
     *      description="Возвращает массив объектов Wallet",
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
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Wallet"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Требуется авторизация"
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      )
     * )
     *
     * Получение списка кошельков пользователя
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getList(Request $request): Response
    {
        $wallets = $request->user()->wallets;

        return new Response(WalletResource::collection($wallets));
    }

    /**
     * @OA\Get(
     *      path="/wallet/{address}",
     *      operationId="get",
     *      tags={"Кошелек"},
     *      summary="Получение информации по кошельку",
     *      description="Возвращает объект Wallet",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Токен",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          description="Адрес кошелька",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен",
     *          @OA\JsonContent(ref="#/components/schemas/Wallet")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Требуется авторизация"
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      )
     * )
     *
     * Получение информации по кошельку
     *
     * @param GetWalletRequest $request
     *
     * @return Response
     */
    public function get(GetWalletRequest $request): Response
    {
        $address = $request->validated()['address'];
        /**
         * @var Wallet $wallet
         */
        $wallet = Wallet::getByAddress($address);
        if (!$wallet->isUserOwner($request->user())) {
            return  new Response(['message' => 'Нельзя получить чужой кошелек'], 401);
        }

        return new Response(new WalletResource($wallet));
    }

    /**
     * @OA\Post(
     *      path="/wallet",
     *      operationId="register",
     *      tags={"Кошелек"},
     *      summary="Добавление кошелька",
     *      description="Добавляет кошелек пользователю",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Токен",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="currency",
     *          description="Валюта кошелька (например: BTC)",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          description="Адрес кошелька",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Требуется авторизация"
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла непредвиденная ошибка"
     *      )
     * )
     *
     * Добавление кошелька
     *
     * @param CreateWalletRequest $request
     *
     * @return Response
     */
    public function create(CreateWalletRequest $request): Response
    {
        $data = $request->validated();
        $user = $request->user();
        $currency = Currency::getByShortName($data['currency']);
        $address = $data['address'];

        try {
            $wallet = WalletRepository::create($user, $currency, $address);
        } catch (\Exception $e) {
            return new Response(['message' => $e->getMessage()], 500);
        }

        return (new Response(['message' => 'Кошелек успешно добавлен', 'wallet' => new WalletResource($wallet)]));
    }
}
