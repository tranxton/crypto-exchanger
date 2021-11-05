<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallet;

use App\Models\Currency;
use App\Models\Wallet\Wallet;
use App\Rules\Wallet\BitcoinAddress;
use App\Rules\Wallet\EthereumAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'currency' => ['required', 'string', Rule::exists(Currency::class, 'short_name')],
            'address'     => [
                'required',
                'string',
                Rule::unique(Wallet::class, 'address'),
                $this->getAddressRuleByCurrencyId($this->request->get('currency_id')),
            ],
        ];

        return $rules;
    }

    /**
     * Возвращает правило валидации адреса в зависимости от выбранной валюты
     *
     * @param int $currency_id
     *
     * @return BitcoinAddress|EthereumAddress
     */
    protected function getAddressRuleByCurrencyId(int $currency_id)
    {
        switch ($currency_id) {
            case Currency::BITCOIN;
                $rule = new BitcoinAddress();
                break;
            case Currency::ETHEREUM:
                $rule = new EthereumAddress();
                break;
            default:
                throw new InvalidArgumentException('Неизвестный тип валюты');
        }

        return $rule;
    }
}
