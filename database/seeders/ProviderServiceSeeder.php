<?php

namespace Database\Seeders;

use App\Models\ProviderService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProviderServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProviderService::create([
            'name'              => 'Logo Painting',
            'category'             => 'Aesthetics',
            'price'             => 500,
        ]);
        
        ProviderService::create([
            'name'              => 'Electrical fix',
            'category'             => 'Electronics',
            'price'             => 1500,
        ]);        
    }
}
