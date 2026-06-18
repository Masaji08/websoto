<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Soto',
            'email' => 'admin@soto.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Soto',
            'email' => 'kasir@soto.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);
    }
}
