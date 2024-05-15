<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the permissions
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class); 
        $this->call(AdminsTableSeeder::class);


    }
}

