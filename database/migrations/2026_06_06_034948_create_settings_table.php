<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'nama_warung', 'value' => 'Soto Seger Boyolali Pak Antok'],
            ['key' => 'deskripsi', 'value' => 'Warung Soto UMKM'],
            ['key' => 'jam_operasional', 'value' => '08:00 - 21:00'],
            ['key' => 'nomor_wa', 'value' => '628123456789'],
            ['key' => 'logo', 'value' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
