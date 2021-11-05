<?php

declare(strict_types=1);

namespace App\Http\Requests\Bill;

use App\Models\Bill\Bill;
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

        return ['id' => $this->route('id')];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'integer', Rule::exists(Bill::class, 'id')],
        ];
    }
}
