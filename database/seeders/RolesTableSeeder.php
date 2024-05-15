<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super-administrator',
            'administrator',
            'operations',
            'customer-service',
            'finance',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
