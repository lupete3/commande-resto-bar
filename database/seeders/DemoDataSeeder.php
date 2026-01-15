<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Establishment;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@bar.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');
        $this->command->info('✓ Super Admin created (admin@bar.com / password)');

        // Create 1 Test Establishment
        $establishment = Establishment::create([
            'name' => 'Le Bar Moderne',
            'slug' => 'le-bar-moderne',
            'type' => 'bar',
            'address' => '123 Rue de Kinshasa, Gombe',
            'phone' => '+243 81 234 5678',
            'email' => 'contact@barmoderne.cd',
            'description' => 'Un bar moderne et convivial',
            'is_active' => true,
            'max_tables' => 50,
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonths(1),
        ]);

        // Create Subscription
        Subscription::create([
            'establishment_id' => $establishment->id,
            'plan' => 'premium',
            'price' => 50,
            'billing_cycle' => 'monthly',
            'started_at' => now(),
            'ends_at' => now()->addMonths(1),
            'status' => 'active',
        ]);

        // Create Manager
        $manager = User::create([
            'name' => 'Gérant Bar Moderne',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'establishment_id' => $establishment->id,
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Create Server
        $server = User::create([
            'name' => 'Serveur Test',
            'email' => 'server@test.com',
            'password' => Hash::make('password'),
            'establishment_id' => $establishment->id,
            'is_active' => true,
        ]);
        $server->assignRole('server');

        $this->command->info("✓ Establishment '{$establishment->name}' created with manager and server");
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('Demo data created successfully!');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('Super Admin: admin@bar.com / password');
        $this->command->info('Manager: manager@test.com / password');
        $this->command->info('Server: server@test.com / password');
        $this->command->info('═══════════════════════════════════════════════');
    }
}
