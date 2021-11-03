<?php

declare(strict_types=1);

namespace App\Http\Requests\Transaction;

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
        return [
            'from'  => ['required', 'string', Rule::exists(Wallet::class, 'address')],
            'to'    => ['required', 'string', Rule::exists(Wallet::class, 'address')],
            'value' => ['required', 'string'],
        ];
    }
}
