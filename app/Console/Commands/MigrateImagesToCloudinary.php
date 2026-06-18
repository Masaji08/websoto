<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use App\Models\Package;
use App\Models\Setting;
use App\Models\Table;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'migrate:images-to-cloudinary
        {--dry-run : Only show what would be done, do not upload}
        {--disk=public : Storage disk to read local files from}';

    protected $description = 'Upload all existing local images to Cloudinary and update database records';

    public function handle(CloudinaryService $cloudinary): int
    {
        $disk = $this->option('disk');
        $dryRun = $this->option('dry-run');
        $total = 0;
        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        $this->newLine();
        $this->line('  ' . ($dryRun ? '[DRY-RUN]' : '') . ' Migrating local images to Cloudinary...');
        $this->newLine();

        // --- Menu Items ---
        $this->info('  ── Menu Items ──');
        MenuItem::whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->orderBy('id')
            ->each(function (MenuItem $item) use ($cloudinary, $disk, $dryRun, &$total, &$uploaded, &$skipped, &$failed) {
                $total++;
                $result = $this->migrateField(
                    $cloudinary, $item->image_path, 'websoto/menu', $disk, $dryRun,
                    fn($url) => $item->updateQuietly(['image_path' => $url])
                );
                match ($result) { 'uploaded' => $uploaded++, 'skipped' => $skipped++, 'failed' => $failed++ };
            });

        // --- Packages ---
        $this->info('  ── Packages ──');
        Package::whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->orderBy('id')
            ->each(function (Package $package) use ($cloudinary, $disk, $dryRun, &$total, &$uploaded, &$skipped, &$failed) {
                $total++;
                $result = $this->migrateField(
                    $cloudinary, $package->image_path, 'websoto/packages', $disk, $dryRun,
                    fn($url) => $package->updateQuietly(['image_path' => $url])
                );
                match ($result) { 'uploaded' => $uploaded++, 'skipped' => $skipped++, 'failed' => $failed++ };
            });

        // --- Settings (logo) ---
        $this->info('  ── Settings (Logo) ──');
        $logo = Setting::where('key', 'logo')->first();
        if ($logo && $logo->value) {
            $total++;
            $result = $this->migrateField(
                $cloudinary, $logo->value, 'websoto/settings', $disk, $dryRun,
                fn($url) => $logo->updateQuietly(['value' => $url])
            );
            match ($result) { 'uploaded' => $uploaded++, 'skipped' => $skipped++, 'failed' => $failed++ };
        }

        // --- Tables (QR codes) ---
        $this->info('  ── Tables (QR Codes) ──');
        Table::whereNotNull('qr_code_path')
            ->where('qr_code_path', '!=', '')
            ->orderBy('id')
            ->each(function (Table $table) use ($cloudinary, $disk, $dryRun, &$total, &$uploaded, &$skipped, &$failed) {
                $total++;
                $result = $this->migrateField(
                    $cloudinary, $table->qr_code_path, 'websoto/qrcodes', $disk, $dryRun,
                    fn($url) => $table->updateQuietly(['qr_code_path' => $url])
                );
                match ($result) { 'uploaded' => $uploaded++, 'skipped' => $skipped++, 'failed' => $failed++ };
            });

        // --- Summary ---
        $this->newLine();
        $this->line('  ─────────────────────────────────────────');
        $this->line('  Summary:');
        $this->line('    Total records  : ' . $total);
        $this->line('    Uploaded       : ' . $uploaded);
        $this->line('    Skipped        : ' . $skipped);
        $this->line('    Failed         : ' . $failed);
        $this->newLine();

        if ($dryRun) {
            $this->warn('  Dry-run completed. No changes were made.');
        }

        return Command::SUCCESS;
    }

    private function migrateField(
        CloudinaryService $cloudinary,
        string $path,
        string $folder,
        string $disk,
        bool $dryRun,
        callable $updateCallback,
    ): string {
        if (CloudinaryService::isCloudinaryUrl($path)) {
            $this->line('    [SKIP] ' . $path . ' (already Cloudinary)');
            return 'skipped';
        }

        $fullPath = Storage::disk($disk)->path($path);

        if (!file_exists($fullPath)) {
            $this->warn('    [SKIP] ' . $path . ' (file not found at ' . $fullPath . ')');
            return 'skipped';
        }

        $filename = basename($path);
        $this->line('    [UPLOAD] ' . $filename);

        if ($dryRun) {
            return 'uploaded';
        }

        try {
            $url = $cloudinary->uploadFromPath($fullPath, $folder);
            $updateCallback($url);
            $this->info('    [DONE] ' . $url);
            return 'uploaded';
        } catch (\Throwable $e) {
            $this->error('    [FAIL] ' . $path . ': ' . $e->getMessage());
            return 'failed';
        }
    }
}
