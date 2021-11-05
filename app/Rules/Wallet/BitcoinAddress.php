<?php

declare(strict_types=1);

namespace App\Rules\Wallet;

use Illuminate\Contracts\Validation\Rule;

class BitcoinAddress implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/([13]|bc1)[A-HJ-NP-Za-km-z1-9]{27,34}$/', (string) $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Неверный адрес bitcoin-кошелька';
    }
}
