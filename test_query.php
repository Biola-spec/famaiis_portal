<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Booted!\n";

use App\Models\User;

$teacher = User::where('usertype', 'Teacher')
    ->orWhere('role', 'Teacher')
    ->orWhereHas('roles', function($q){
        $q->where('name', 'Teacher');
    })->first();

if ($teacher) {
    echo "Testing updated PDF template for Teacher ID: {$teacher->id} ({$teacher->name})\n";
    $controller = new \App\Http\Controllers\Backend\Setup\AssignSubjectController();
    $pdf = $controller->TeacherAssignmentPdf($teacher->id);
    echo "PDF generated successfully! Output size: " . strlen($pdf) . " bytes\n";
} else {
    echo "No teacher found to test.\n";
}

