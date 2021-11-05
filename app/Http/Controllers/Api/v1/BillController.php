<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Bill\GetRequest as GetBillRequest;
use App\Http\Requests\Bill\CreateRequest as CreateBillRequest;
use App\Models\Wallet\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillController extends ApiController
{
    public function get(GetBillRequest $request): Response
    {
        //Получение транзакции
    }

    public function getList(Request $request): Response
    {
        //Получение списка транзакций
    }

    public function create(CreateBillRequest $request)
    {
        $bill_data = $request->validated();

        $user = $request->user();
        $sender_wallet = Wallet::getByAddress($bill_data['sender_wallet_address']);
        $recipient_wallet = Wallet::getByAddress($bill_data['recipient_wallet_address']);
        $value = $bill_data['value'];

        if (!$sender_wallet->isUserOwner($user)) {
            return new Response(['message' => 'Нельзя совершить перевод с чужого кошелька'], 401);
        }

        if ($sender_wallet->address === $recipient_wallet->address) {
            return new Response(['message' => 'Кошельки отправителя и получателя должны отличаться'], 422);
        }

        if (bccomp($sender_wallet->value, $value, null) === -1) {
            return new Response(['message' => 'Недостаточно средств для создания перевода'], 403);
        }

        return new Response('Nothing to show');
    }

    public function accept(Request $request)
    {
        //Подтверждение транзакции
    }
}
