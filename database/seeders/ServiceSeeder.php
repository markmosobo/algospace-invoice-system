<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('services')->insert([

            /*
            |--------------------------------------------------------------------------
            | Printing & Copying
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Black & White Printing / Copying',
                'category' => 'Printing & Copying',
                'price' => 10,
                'unit' => 'page',
                'is_bundle' => false,
            ],
            [
                'name' => 'Color Printing / Copying',
                'category' => 'Printing & Copying',
                'price' => 30,
                'unit' => 'page',
                'is_bundle' => false,
            ],
            [
                'name' => 'Lamination (A4)',
                'category' => 'Printing & Copying',
                'price' => 100,
                'unit' => 'document',
                'is_bundle' => false,
            ],
            [
                'name' => 'Binding',
                'category' => 'Printing & Copying',
                'price' => 100,
                'unit' => 'document',
                'is_bundle' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Typing & Documents
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Typing',
                'category' => 'Typing & Documents',
                'price' => 50,
                'unit' => 'page',
                'is_bundle' => false,
            ],
            [
                'name' => 'Resume / CV Typing',
                'category' => 'Typing & Documents',
                'price' => 150,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'Letter / Form Filling',
                'category' => 'Typing & Documents',
                'price' => 100,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'Academic Work',
                'category' => 'Typing & Documents',
                'price' => 100,
                'unit' => 'page',
                'is_bundle' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Online Applications
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'KUCCPS Application',
                'category' => 'Online Applications',
                'price' => 300,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'KRA Services',
                'category' => 'Online Applications',
                'price' => 300,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'HELB Application',
                'category' => 'Online Applications',
                'price' => 300,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'Pension Application',
                'category' => 'Online Applications',
                'price' => 400,
                'unit' => 'service',
                'is_bundle' => false,
            ],            
            [
                'name' => 'Passport Application',
                'category' => 'Online Applications',
                'price' => 500,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'NSSF / NHIF Services',
                'category' => 'Online Applications',
                'price' => 250,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'Good Conduct Certificate',
                'category' => 'Online Applications',
                'price' => 500,
                'unit' => 'service',
                'is_bundle' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Internet & Computer Use
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Computer Access',
                'category' => 'Internet & Computer Use',
                'price' => 50,
                'unit' => 'hour',
                'is_bundle' => false,
            ],
            [
                'name' => 'Internet Access',
                'category' => 'Internet & Computer Use',
                'price' => 50,
                'unit' => 'hour',
                'is_bundle' => false,
            ],
            [
                'name' => 'USB Printing',
                'category' => 'Internet & Computer Use',
                'price' => 20,
                'unit' => 'page',
                'is_bundle' => false,
            ],
            [
                'name' => 'Scanning',
                'category' => 'Internet & Computer Use',
                'price' => 30,
                'unit' => 'page',
                'is_bundle' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Other Services
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Graphic Design',
                'category' => 'Other Services',
                'price' => 300,
                'unit' => 'page',
                'is_bundle' => false,
            ],
            [
                'name' => 'Document Editing',
                'category' => 'Other Services',
                'price' => 200,
                'unit' => 'service',
                'is_bundle' => false,
            ],
            [
                'name' => 'Mobile Money / Payments',
                'category' => 'Other Services',
                'price' => 50,
                'unit' => 'transaction',
                'is_bundle' => false,
            ],
            [
                'name' => 'Data Recovery / Software Services',
                'category' => 'Other Services',
                'price' => 500,
                'unit' => 'service',
                'is_bundle' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Best Value Bundles
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Printing + Typing Bundle',
                'category' => 'Bundles',
                'price' => 200,
                'unit' => 'bundle',
                'is_bundle' => true,
            ],
            [
                'name' => 'Application Bundle (KUCCPS + KRA + HELB)',
                'category' => 'Bundles',
                'price' => 800,
                'unit' => 'bundle',
                'is_bundle' => true,
            ],

        ]);
    }
}
