<?php
use App\Models\StudentMarks;
use Illuminate\Support\Facades\Route;

Route::get('/debug-marks', function() {
    $marks = StudentMarks::with(['subject', 'assign_subject.school_subject'])
        ->where('id_no', '20260001')
        ->get();
    
    return $marks;
});
