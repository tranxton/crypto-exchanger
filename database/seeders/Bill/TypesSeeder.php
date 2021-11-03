<?php

namespace Database\Seeders\Bill;

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
            'name' => 'transfer'
        ];

        DB::table('bill_types')->insert($types);
    }
}
