<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            TableSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            PackageSeeder::class,
        ]);
    }
}
