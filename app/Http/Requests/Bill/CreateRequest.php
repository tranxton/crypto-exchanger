<?php

declare(strict_types=1);

namespace App\Http\Requests\Bill;

use App\Models\Bill\Bill;
use App\Models\Bill\Type;
use App\Models\Wallet\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $min = Bill::MIN_TRANSFER;
        $max = Bill::MAX_TRANSFER;
        return [
            'sender_wallet_address'    => ['required', 'string', Rule::exists(Wallet::class, 'address')],
            'recipient_wallet_address' => ['required', 'string', Rule::exists(Wallet::class, 'address')],
            'value'                    => ['required', 'numeric', "between:{$min},{$max}"],
            'type'                     => ['required', 'integer', Rule::exists(Type::class, 'id')],
        ];
    }
}
