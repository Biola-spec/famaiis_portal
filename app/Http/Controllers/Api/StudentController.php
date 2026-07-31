<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentMarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // 1. Calculate Average Mark
        $avgMark = StudentMarks::where('student_id', $user->id)
            ->avg('marks');
            
        // 2. Mock Attendance (since full attendance logic might be complex)
        // In a real app, we'd query the attendance table
        $attendance = "92%"; // Placeholder for now, but we can make it dynamic if table exists
        
        // 3. Get Recent Activities (Real database records)
        // For example, recent marks updated or assignments
        $recentMarks = StudentMarks::with('subject')
            ->where('student_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(function($mark) {
                return [
                    'id' => $mark->id,
                    'title' => ($mark->subject->name ?? 'Subject') . " Mark Updated",
                    'date' => $mark->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'stats' => [
                'attendance' => $attendance,
                'average_mark' => round($avgMark ?? 0, 1) . '%',
                'upcoming_exams' => 0, // Mock for now
            ],
            'recent_activities' => $recentMarks->isEmpty() ? [
                ['id' => 0, 'title' => 'No recent activities', 'date' => 'Now']
            ] : $recentMarks
        ]);
    }
}
