<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentMarks;

$marks = StudentMarks::where('id_no', '20260001')->get();

foreach ($marks as $mark) {
    echo "ID: {$mark->id}, SubID: {$mark->subject_id}, AssignSubID: {$mark->assign_subject_id}, Marks: {$mark->marks}, CA: {$mark->ca_score}, Exam: {$mark->exam_score}, Project: {$mark->project_score}, Created: {$mark->created_at}\n";
}
