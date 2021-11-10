<?php

namespace Database\Seeders\User;

use App\Models\Referral\ChargeStatus;
use Illuminate\Database\Seeder;

class ReferralChargeStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            ['name' => 'created'],
            ['name' => 'charged'],
            ['name' => 'failed'],
        ];

        ChargeStatus::insert($levels);
    }
}
