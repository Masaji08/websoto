<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $items = MenuItem::all()->keyBy('name');

        $packages = [
            [
                'name' => 'Paket Soto Ayam',
                'description' => '1 Soto Ayam Campur Nasi + 1 Perkedel + 1 Es Teh Manis + 1 Kerupuk',
                'price' => 18000,
                'sort_order' => 1,
                'components' => [
                    ['menu_item' => $items['Soto Ayam Campur Nasi'], 'qty' => 1],
                    ['menu_item' => $items['Perkedel'], 'qty' => 1],
                    ['menu_item' => $items['Es Teh Manis'], 'qty' => 1],
                    ['menu_item' => $items['Kerupuk'], 'qty' => 1],
                ],
            ],
            [
                'name' => 'Paket Soto Daging',
                'description' => '1 Soto Daging Campur Nasi + 1 Perkedel + 1 Air Jeruk Hangat + 1 Kerupuk',
                'price' => 22000,
                'sort_order' => 2,
                'components' => [
                    ['menu_item' => $items['Soto Daging Campur Nasi'], 'qty' => 1],
                    ['menu_item' => $items['Perkedel'], 'qty' => 1],
                    ['menu_item' => $items['Air Jeruk Hangat'], 'qty' => 1],
                    ['menu_item' => $items['Kerupuk'], 'qty' => 1],
                ],
            ],
            [
                'name' => 'Paket Nasi + Sate',
                'description' => '1 Nasi + 2 Sate Daging + 1 Es Teh Manis',
                'price' => 18000,
                'sort_order' => 3,
                'components' => [
                    ['menu_item' => $items['Nasi'], 'qty' => 1],
                    ['menu_item' => $items['Sate Daging'], 'qty' => 2],
                    ['menu_item' => $items['Es Teh Manis'], 'qty' => 1],
                ],
            ],
            [
                'name' => 'Paket Keluarga',
                'description' => '2 Soto Ayam Campur Nasi + 2 Sate Daging + 2 Es Teh Manis + 2 Kerupuk',
                'price' => 40000,
                'sort_order' => 4,
                'components' => [
                    ['menu_item' => $items['Soto Ayam Campur Nasi'], 'qty' => 2],
                    ['menu_item' => $items['Sate Daging'], 'qty' => 2],
                    ['menu_item' => $items['Es Teh Manis'], 'qty' => 2],
                    ['menu_item' => $items['Kerupuk'], 'qty' => 2],
                ],
            ],
            [
                'name' => 'Paket Hemat 2 Orang',
                'description' => '2 Soto Ayam Nasi Pisah + 2 Es Teh Manis + 2 Kerupuk',
                'price' => 35000,
                'sort_order' => 5,
                'components' => [
                    ['menu_item' => $items['Soto Ayam Nasi Pisah'], 'qty' => 2],
                    ['menu_item' => $items['Es Teh Manis'], 'qty' => 2],
                    ['menu_item' => $items['Kerupuk'], 'qty' => 2],
                ],
            ],
        ];

        foreach ($packages as $data) {
            $components = $data['components'];
            unset($data['components']);

            $originalPrice = collect($components)->sum(fn ($c) => $c['menu_item']->price * $c['qty']);
            $data['original_price'] = $originalPrice;

            $package = Package::create($data);

            foreach ($components as $comp) {
                $package->items()->create([
                    'menu_item_id' => $comp['menu_item']->id,
                    'quantity' => $comp['qty'],
                ]);
            }
        }
    }
}
