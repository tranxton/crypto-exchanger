<?php

declare(strict_types=1);

namespace App\Rules\Wallet;

use Illuminate\Contracts\Validation\Rule;

class EthereumAddress implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^0x[a-fA-F0-9]{40}$/', (string) $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Неверный адрес Ethereum кошелька';
    }
}
