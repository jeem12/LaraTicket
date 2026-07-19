<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'IT',
        ]);

        User::create([
            'name' => 'Standard User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'department' => 'Support',
        ]);
    }
}