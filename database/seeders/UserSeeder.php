<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'AlgoSpace Office',
            'email'             => 'office@algospace.co.ke',
            'email_verified_at' => Carbon::now(),
            'role'              => 'office',
            'password'          => Hash::make('password123'),
        ]);

        User::create([
            'name'              => 'Personal Account',
            'email'             => 'personal@algospace.co.ke',
            'email_verified_at' => Carbon::now(),
            'role'              => 'personal',
            'password'          => Hash::make('password123'),
        ]);
    }
}
