<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Booted!\n";


use App\Models\User;
use Illuminate\Support\Facades\Schema;

echo "Columns:\n";
print_r(Schema::getColumnListing('users'));


$students = User::where('usertype', 'Student')->get();

echo "Total students: " . $students->count() . "\n";
$with_image = 0;
foreach ($students as $student) {
    if ($student->image) {
        $with_image++;
        echo "ID: {$student->id}, Name: {$student->name}, Image: {$student->image}\n";
    }
}
echo "Students with image: $with_image\n";


