<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$setting = \App\Models\SiteSetting::find(1);
if ($setting) {
    echo "SITE SETTING:\n";
    print_r($setting->toArray());
} else {
    echo "No SiteSetting found.\n";
}

$sec = \App\Models\SecondarySetting::find(1);
if ($sec) {
    echo "\nSECONDARY SETTING:\n";
    print_r($sec->toArray());
} else {
    echo "No SecondarySetting found.\n";
}
