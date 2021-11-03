<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\User\User;
use App\Rules\CheckPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => ['required', Rule::exists(User::class, 'name')],
            'password' => ['required', new CheckPassword($this->get('name'))],
        ];
    }
}
