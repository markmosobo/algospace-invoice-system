<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuppliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('supplies')->insert([
            [
                'id' => 1,
                'supplier_id' => 1, // AlgoSpace Stationers
                'item' => 'A4 Printing Papers (Ream)',
                'unit_price' => 850,
                'quantity' => 2,
                'total' => 1700.00,
                'payment_date' => Carbon::now(),
                'payment_method' => 'cash',
                'status' => 'replenisheble',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'supplier_id' => 2, // Epson Dealer
                'item' => 'Printer Ink Cartridge',
                'unit_price' => 3200,
                'quantity' => 1,
                'total' => 3200.00,
                'payment_date' => Carbon::now(),
                'payment_method' => 'mpesa',
                'status' => 'replenisheble',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'supplier_id' => 3, // Safaricom
                'item' => '4G Internet Bundle',
                'unit_price' => 3000,
                'quantity' => 1,
                'total' => 3000.00,
                'payment_date' => Carbon::now(),
                'payment_method' => 'mpesa',
                'status' => 'onetime',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'supplier_id' => 4, // Local Wholesale
                'item' => 'Lamination Pouches',
                'unit_price' => 50,
                'quantity' => 40,
                'total' => 2000.00,
                'payment_date' => Carbon::now(),
                'payment_method' => 'cash',
                'status' => 'replenisheble',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
