<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\User\User;
use App\Rules\IsUserPasswordValid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => ['required', 'string', Rule::exists(User::class, 'name')],
            'password' => ['required', 'string', new IsUserPasswordValid($this->get('name'))],
        ];
    }
}
