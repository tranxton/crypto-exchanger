<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            ['full_name' => 'Bitcoin', 'short_name' => 'BTC', 'address_length' => 34],
            ['full_name' => 'Ethereum', 'short_name' => 'ETH', 'address_length' => 42],
        ];

        Currency::insert($currencies);
    }
}
