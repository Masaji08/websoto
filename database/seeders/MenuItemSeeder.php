<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::where('name', 'Makanan')->first();
        $tambahan = Category::where('name', 'Menu Tambahan')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        $items = [
            // Makanan
            ['category_id' => $makanan->id, 'name' => 'Soto Ayam Campur Nasi', 'price' => 12000],
            ['category_id' => $makanan->id, 'name' => 'Soto Ayam Nasi Pisah', 'price' => 15000],
            ['category_id' => $makanan->id, 'name' => 'Soto Daging Campur Nasi', 'price' => 13000],
            ['category_id' => $makanan->id, 'name' => 'Soto Daging Nasi Pisah', 'price' => 16000],
            ['category_id' => $makanan->id, 'name' => 'Soto Ayam Tanpa Nasi', 'price' => 12000],
            ['category_id' => $makanan->id, 'name' => 'Soto Daging Tanpa Nasi', 'price' => 13000],
            ['category_id' => $makanan->id, 'name' => 'Nasi', 'price' => 5000],
            ['category_id' => $makanan->id, 'name' => 'Sosis Solo', 'price' => 4000],
            ['category_id' => $makanan->id, 'name' => 'Mendoan', 'price' => 2500],
            ['category_id' => $makanan->id, 'name' => 'Tahu Goreng', 'price' => 2500],
            ['category_id' => $makanan->id, 'name' => 'Perkedel', 'price' => 3500],
            ['category_id' => $makanan->id, 'name' => 'Bacem Tahu', 'price' => 2500],
            ['category_id' => $makanan->id, 'name' => 'Bacem Tempe', 'price' => 2500],
            ['category_id' => $makanan->id, 'name' => 'Telor Asin', 'price' => 6000],
            ['category_id' => $makanan->id, 'name' => 'Kerupuk', 'price' => 2000],

            // Menu Tambahan
            ['category_id' => $tambahan->id, 'name' => 'Nasi', 'price' => 5000],
            ['category_id' => $tambahan->id, 'name' => 'Sosis Solo', 'price' => 4000],
            ['category_id' => $tambahan->id, 'name' => 'Mendoan', 'price' => 2500],
            ['category_id' => $tambahan->id, 'name' => 'Tahu Goreng', 'price' => 2500],
            ['category_id' => $tambahan->id, 'name' => 'Perkedel', 'price' => 3500],
            ['category_id' => $tambahan->id, 'name' => 'Bacem Tahu', 'price' => 2500],
            ['category_id' => $tambahan->id, 'name' => 'Bacem Tempe', 'price' => 2500],
            ['category_id' => $tambahan->id, 'name' => 'Telor Asin', 'price' => 6000],
            ['category_id' => $tambahan->id, 'name' => 'Kerupuk', 'price' => 2000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Daging', 'price' => 8000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Paru', 'price' => 5000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Kerang', 'price' => 8000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Ati Ampela', 'price' => 5000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Usus', 'price' => 5000],
            ['category_id' => $tambahan->id, 'name' => 'Sate Telur Puyuh', 'price' => 5000],

            // Minuman
            ['category_id' => $minuman->id, 'name' => 'Teh Tawar Hangat', 'price' => 2000],
            ['category_id' => $minuman->id, 'name' => 'Teh Tawar Es', 'price' => 3000],
            ['category_id' => $minuman->id, 'name' => 'Teh Manis Hangat', 'price' => 4000],
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis', 'price' => 5000],
            ['category_id' => $minuman->id, 'name' => 'Es Lemon Tea', 'price' => 6000],
            ['category_id' => $minuman->id, 'name' => 'Air Jeruk Hangat', 'price' => 8000],
            ['category_id' => $minuman->id, 'name' => 'Air Jeruk Es', 'price' => 7000],
            ['category_id' => $minuman->id, 'name' => 'Teh Pucuk', 'price' => 5000],
            ['category_id' => $minuman->id, 'name' => 'Ades / Aqua / Le Minerale', 'price' => 4000],
            ['category_id' => $minuman->id, 'name' => 'Kopi', 'price' => 7000],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
