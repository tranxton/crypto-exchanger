<?php

namespace Database\Seeders\Bill;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            ['name' => 'created'],
            ['name' => 'accepted'],
            ['name' => 'completed'],
            ['name' => 'failed'],
            ['name' => 'expired'],
        ];

        DB::table('bill_statuses')->insert($statuses);
    }
}
