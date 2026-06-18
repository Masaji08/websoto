<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $qrService = app(QrCodeService::class);

        for ($i = 1; $i <= 13; $i++) {
            $name = "Meja $i";
            $table = Table::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'is_active' => true,
            ]);

            $qrService->generate($table);
        }
    }
}
