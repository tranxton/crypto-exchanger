<?php

namespace Database\Seeders\User;

use App\Models\Referral\Level;
use Illuminate\Database\Seeder;

class ReferralLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            ['name' => '1-ый уровень', 'percent' => '15.0'],
            ['name' => '2-ый уровень', 'percent' => '10.0'],
            ['name' => '3-ый уровень', 'percent' => '8.00'],
            ['name' => '4-ый уровень', 'percent' => '6.00'],
            ['name' => '5-ый уровень', 'percent' => '4.00'],
            ['name' => '6-ый уровень', 'percent' => '3.00'],
            ['name' => '7-ый уровень', 'percent' => '2.00'],
            ['name' => '8-ый уровень', 'percent' => '1.00'],
            ['name' => '9-ый уровень', 'percent' => '0.50'],
            ['name' => '10-ый уровень', 'percent' => '0.50'],
        ];

        Level::insert($levels);
    }
}
