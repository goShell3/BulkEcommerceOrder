<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create admin role
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin'
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);

        // Assign admin role to user
        $admin->roles()->attach($adminRole);
    }
} 