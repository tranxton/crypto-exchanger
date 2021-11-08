<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Models\Wallet\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetRequest extends FormRequest
{
    /**
     * @param null $keys
     *
     * @return array
     */
    public function all($keys = null): array
    {
        parent::all($keys);

        return ['address' => $this->route('address')];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => ['required', 'string', Rule::exists(Wallet::class, 'address')],
        ];
    }
}
