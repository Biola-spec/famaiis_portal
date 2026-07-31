<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
$roles = DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->join('users', 'role_user.user_id', '=', 'users.id')
    ->select('users.name', 'roles.name as role_name')
    ->get();

foreach ($roles as $r) {
    echo "User: {$r->name}, Role: {$r->role_name}\n";
}
