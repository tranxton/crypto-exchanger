<?php

namespace Database\Seeders;

use App\Models\Wallet\Type;
use Illuminate\Database\Seeder;

class WalletTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Системный'],
            ['name' => 'Пользовательский'],
        ];

        Type::insert($types);
    }
}
