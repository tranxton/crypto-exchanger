<?php

namespace Database\Seeders\Transaction;

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
            ['name' => 'blocked'],
            ['name' => 'completed'],
            ['name' => 'failed'],
            ['name' => 'expired'],
        ];

        DB::table('transaction_statuses')->insert($statuses);
    }
}
