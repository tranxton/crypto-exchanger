<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\Database\Seeders\User\ReferralLevelsSeeder::class);
        $this->call(\Database\Seeders\User\TypesSeeder::class);
        $this->call(\Database\Seeders\User\Seeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(\Database\Seeders\Wallet\TypesSeeder::class);
        $this->call(\Database\Seeders\Wallet\Seeder::class);
        $this->call(\Database\Seeders\Bill\TypesSeeder::class);
        $this->call(\Database\Seeders\Bill\StatusesSeeder::class);
        $this->call(\Database\Seeders\Transaction\TypesSeeder::class);
        $this->call(\Database\Seeders\Transaction\StatusesSeeder::class);
        $this->call(\Database\Seeders\User\ReferralChargeStatusesSeeder::class);
    }
}
