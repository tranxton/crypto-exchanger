<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Wallet\CreateRequest as CreateWalletRequest;
use App\Http\Requests\Wallet\GetRequest as GetWalletRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletController extends ApiController
{
    public function getList(Request $request): Response
    {
        //Список кошельков пользователя
    }

    public function get(GetWalletRequest $request): Response
    {
        //Кошелек пользователя
    }

    public function create(CreateWalletRequest $request): Response
    {
        $data = $request->validated();
    }
}
