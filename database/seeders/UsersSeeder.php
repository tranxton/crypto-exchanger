<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name'              => env('SYSTEM_NAME', 'system'),
                'email'             => env('SYSTEM_EMAIL', 'system'),
                'email_verified_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'password'          => Hash::make(env('SYSTEM_PASSWORD', Str::random(20))),
            ],
        ];

        DB::table('users')->insert($user);
    }
}
