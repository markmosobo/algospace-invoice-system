<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'name' => 'AlgoSpace Stationers',
                'phone' => '0712345678',
                'email' => 'algospace@suppliers.com',
                'address' => 'Kapsokwony Town',
            ],
            [
                'id' => 2,
                'name' => 'Epson Authorized Dealer',
                'phone' => '0723456789',
                'email' => 'epson@dealers.com',
                'address' => 'Bungoma',
            ],
            [
                'id' => 3,
                'name' => 'Safaricom Enterprise',
                'phone' => '0700123456',
                'email' => 'safaricom@enterprise.co.ke',
                'address' => 'Nairobi',
            ],
            [
                'id' => 4,
                'name' => 'Local Wholesale Shop',
                'phone' => '0798765432',
                'email' => null, // nullable ✔
                'address' => 'Kapsokwony Market',
            ],
        ]);
    }
}
