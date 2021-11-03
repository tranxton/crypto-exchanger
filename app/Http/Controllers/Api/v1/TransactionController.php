<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Transaction\GetRequest as GetTransactionRequest;
use App\Http\Requests\Wallet\CreateRequest as CreateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends ApiController
{
    public function get(GetTransactionRequest $request): Response
    {
        //Получение транзакции
    }

    public function getList(Request $request): Response
    {
        //Получение списка транзакций
    }

    public function create(CreateTransactionRequest $request)
    {
        //Создание транзакции
    }

    public function accept(Request $request)
    {
        //Подтверждение транзакции
    }
}
