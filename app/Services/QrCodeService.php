<?php

namespace App\Services;

use App\Models\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generate(Table $table): string
    {
        $url = route('menu.index', $table->slug);

        $qrCode = QrCode::format('svg')
            ->size(400)
            ->errorCorrection('H')
            ->generate($url);

        $filename = 'qr-' . $table->slug . '.svg';
        $path = 'qrcodes/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $qrCode);

        $table->update(['qr_code_path' => 'storage/qrcodes/' . $filename]);

        return 'storage/qrcodes/' . $filename;
    }
}
