<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Helpers\FloatHelper;
use App\Http\Requests\Bill\GetRequest as BillGetRequest;
use App\Http\Requests\Bill\GetRequest as GetBillRequest;
use App\Http\Requests\Bill\CreateRequest as CreateBillRequest;
use App\Http\Resources\BillResource;
use App\Models\User\User;
use App\Repositories\BillRepository;
use App\Repositories\WalletRepository;
use App\Services\BillService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillController extends ApiController
{
    use FloatHelper;


    /**
     * @OA\Get(
     *      path="/bill/all",
     *      operationId="getList",
     *      tags={"Перевод"},
     *      summary="Получение информации по кошельку",
     *      description="Возвращает массив объектов Bill",
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
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Bill"))
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
     * Получение списка всех переводов созданных пользователем
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getList(Request $request): Response
    {
        try {
            /**
             * @var User $user
             */
            $user = $request->user();
            $bills = BillRepository::all($user);
        } catch (Exception $e) {
            return new Response(['message' => 'Не удалось получить список счетов', 500]);
        }

        return new Response(BillResource::collection($bills));
    }

    /**
     * @OA\Get(
     *      path="/bill/{id}",
     *      operationId="get",
     *      tags={"Перевод"},
     *      summary="Получение информации по переводу",
     *      description="Возвращает объект Bill",
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
     *          @OA\JsonContent(ref="#/components/schemas/Bill")
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
     * Получение информации по переводу
     *
     * @param GetBillRequest $request
     *
     * @return Response
     */
    public function get(GetBillRequest $request): Response
    {
        $id = (int) $request->validated()['id'];
        $user = $request->user();


        try {
            $bill = (new BillService($user))->get($id);
        } catch (Exception $e) {
            return new Response(['message' => $e->getMessage()], 500);
        }

        return new Response(new BillResource($bill));
    }

    /**
     * @OA\Post(
     *      path="/bill",
     *      operationId="create",
     *      tags={"Перевод"},
     *      summary="Создание запроса на перевод",
     *      description="Создает запрос на перевод",
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
     *          name="sender_wallet_address",
     *          description="Адрес кошелька отправителя",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="recipient_wallet_address",
     *          description="Адрес кошелька получателя",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="value",
     *          description="Сумма перевода",
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
     * Создание перевода
     *
     * @param CreateBillRequest $request
     *
     * @return Response
     */
    public function create(CreateBillRequest $request)
    {
        $bill_data = $request->validated();

        $user = $request->user();
        $sender_wallet = WalletRepository::getByAddress($bill_data['sender_wallet_address']);
        $recipient_wallet = WalletRepository::getByAddress($bill_data['recipient_wallet_address']);
        $value = $this->toString($bill_data['value']);

        try {
            $bill = (new BillService($user))->create($sender_wallet, $recipient_wallet, $value);
        } catch (Exception $e) {
            return new Response(['message' => $e->getMessage()], $e->getCode());
        }
        $message = [
            'message' => 'Запрос на перевод успешно создан',
            'bill'    => new BillResource($bill),
        ];

        return new Response($message, 201);
    }

    /**
     * @OA\Post(
     *      path="/bill/{id}",
     *      operationId="accept",
     *      tags={"Перевод"},
     *      summary="Подтверждение перевода",
     *      description="Подтверждает перевод",
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
     * Подтверждение перевода
     *
     * @param GetBillRequest $request
     *
     * @return Response
     */
    public function accept(BillGetRequest $request): Response
    {
        $id = (int) $request->validated()['id'];
        $user = $request->user();
        $bill = BillRepository::get($id);

        try {
            $bill = (new BillService($user))->accept($bill);
        } catch (Exception $e) {
            return new Response(['message' => $e->getMessage()], 500);
        }

        return new Response(new BillResource($bill));
    }
}
