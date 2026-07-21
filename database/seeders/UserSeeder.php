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
            'prefix' => 'Dr.',
            'first_name' => 'Admin',
            'middle_name' => 'A.',
            'last_name' => 'User',
            'suffix' => 'Jr.',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => '1',
        ]);

        User::create([
            'prefix' => 'Mr.',
            'first_name' => 'Standard',
            'middle_name' => 'B.',
            'last_name' => 'User',
            'suffix' => null,
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'department' => '2',
        ]);
    }
}