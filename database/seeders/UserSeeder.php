<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@shezalaundry.com'],
            [
                'name'     => 'Admin Sheza',
                'email'    => 'admin@shezalaundry.com',
                'password' => Hash::make('admin123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@shezalaundry.com'],
            [
                'name'     => 'Kasir 1',
                'email'    => 'kasir@shezalaundry.com',
                'password' => Hash::make('kasir123'),
            ]
        );
    }
}
