<?php

namespace Database\Seeders\Wallet;

use App\Models\Wallet\Type;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
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
