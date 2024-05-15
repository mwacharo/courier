<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Admin::create([
            'first_name' => 'Rono',
            'last_name' => 'Douglas',
            'email' => 'itservices@boxleocourier.com',
            'phone_number' => '+254110666140',
            'profile_image' => null,
            'date_of_birth' => now(),
            'is_enabled' => true,
            'role' => 'super-administrator',
            'password' => Hash::make('password'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
