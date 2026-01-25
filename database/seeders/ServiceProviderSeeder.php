<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProviderSeeder extends Seeder
{
    public function run()
    {
        $providers = [
            [
                'name'  => 'Kenya Power',
                'phone' => '0700000000',
                'email' => 'support@kenyapower.co.ke',
            ],
            [
                'name'  => 'Safaricom',
                'phone' => '0710000000',
                'email' => 'support@safaricom.co.ke',
            ],
            [
                'name'  => 'Zuku',
                'phone' => '0720000000',
                'email' => 'support@zuku.co.ke',
            ],
            [
                'name'  => 'Econet',
                'phone' => '0730000000',
                'email' => 'support@econet.co.ke',
            ],
            [
                'name'  => 'Local Vendor',
                'phone' => '0740000000',
                'email' => 'vendor@example.com',
            ],
        ];

        foreach ($providers as $provider) {
            ServiceProvider::create($provider);
        }
    }
}
