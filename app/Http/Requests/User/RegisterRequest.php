<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\Referral\Link as ReferralLink;
use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => ['required', 'string', 'min:5', 'max:30'],
            'email'         => ['required', 'string', 'email:rfc', Rule::unique(User::class, 'email')],
            'password'      => ['required', Password::min(8)->numbers()->letters()->symbols()->mixedCase()],
            'referral_link' => ['filled', 'string', 'min:5', 'max:5', Rule::exists(ReferralLink::class, 'value')],
        ];
    }
}
