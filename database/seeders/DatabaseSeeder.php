<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call other seeders in the correct order
        $this->call([
            RoleSeeder::class,          // Create roles first
            ProdutoSeeder::class,        // Populate products
            CotaRegularSeeder::class,    // Populate regular quotas
            FilamentAdminSeeder::class,  // Create Filament admin user
        ]);
    }
}
