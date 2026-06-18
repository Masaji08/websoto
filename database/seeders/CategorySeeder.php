<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Menu Tambahan', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Minuman', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
