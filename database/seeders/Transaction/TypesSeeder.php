<?php

namespace Database\Seeders\Transaction;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            ['name' => 'transfer'],
            ['name' => 'commission'],
            ['name' => 'bonus']
        ];

        DB::table('transaction_types')->insert($types);
    }
}
