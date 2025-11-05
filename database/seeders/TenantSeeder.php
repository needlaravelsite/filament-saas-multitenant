<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'id' => 'tenant1',
                'name' => 'Acme Corporation',
                'email' => 'admin@tenant1.com',
                'primary_color' => '#3b82f6',
                'secondary_color' => '#10b981',
                'is_active' => true,
            ],
            [
                'id' => 'tenant2',
                'name' => 'Global Tech Solutions',
                'email' => 'admin@tenant2.com',
                'primary_color' => '#8b5cf6',
                'secondary_color' => '#f59e0b',
                'is_active' => true,
            ],
            [
                'id' => 'tenant3',
                'name' => 'Innovation Labs',
                'email' => 'admin@tenant3.com',
                'primary_color' => '#ef4444',
                'secondary_color' => '#06b6d4',
                'is_active' => true,
            ],
        ];

        foreach ($tenants as $tenantData) {
            $tenant = Tenant::create($tenantData);
            
            // Create domain for each tenant
            $tenant->domains()->create([
                'domain' => $tenantData['id'] . '.localhost',
            ]);

            // Run tenant migrations
            $this->command->info("Creating database for tenant: {$tenant->name}");
        }

        $this->command->info('Tenants created successfully!');
        $this->command->info('Access tenants at:');
        $this->command->info('- http://tenant1.localhost/admin');
        $this->command->info('- http://tenant2.localhost/admin');
        $this->command->info('- http://tenant3.localhost/admin');
    }
}
