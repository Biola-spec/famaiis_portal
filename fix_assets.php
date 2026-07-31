<?php
$file = __DIR__ . '/resources/views/frontend/secondary.blade.php';
$content = file_get_contents($file);

// Find all occurrences of src="{{ asset('secondary_web/assets/images/... "
// and add the missing ') }}
$content = preg_replace('/src="\{\{\s*asset\(\'secondary_web\/assets\/images\/([^"]+)"/', 'src="{{ asset(\'secondary_web/assets/images/$1\') }}"', $content);

file_put_contents($file, $content);
echo "Fixed.\n";
