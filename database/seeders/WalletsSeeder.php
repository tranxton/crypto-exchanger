<?php

namespace Database\Seeders;

use App\Models\Wallet\Wallet;
use Illuminate\Database\Seeder;

class WalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wallet = [
            ['user_id' => 1, 'currency_id' => 1, 'type_id' => 1, 'address' => 'BTC-ADDRESS', 'value' => '0.00'],
            ['user_id' => 1, 'currency_id' => 2, 'type_id' => 1, 'address' => 'ETH-ADDRESS', 'value' => '0.00'],
        ];

        Wallet::insert($wallet);
    }
}
