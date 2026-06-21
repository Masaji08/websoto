<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Logo value: " . (DB::table('settings')->where('key', 'logo')->value('value') ?? '(null)') . "\n";
echo "DONE\n";
