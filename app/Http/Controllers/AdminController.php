<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\EmployeeAttendance;

class AdminController extends Controller
{
    public function Logout(Request $request){
    	Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    	return Redirect()->route('login');
    }

    public function Index(){
        $user = Auth::user();
        if ($user->role === 'Teacher' || $user->role === 'Staff' || $user->hasRole('Teacher', 'Staff')) {
            // Fetch classes assigned to this teacher as a class teacher
            $assignedClassIds = \App\Models\AssignClassTeacher::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();
                
            // Fetch students in those classes for the current active session
            $currentSession = \App\Models\StudentYear::where('is_active', 1)->first();
            $sessionId = $currentSession ? $currentSession->id : null;
            
            if ($assignedClassIds->isNotEmpty() && $sessionId) {
                $studentsAssigned = \App\Models\AssignStudent::with('student', 'student_class')
                    ->where('year_id', $sessionId)
                    ->whereIn('class_id', $assignedClassIds)
                    ->get();
                    
                $data['teacher_total_students'] = $studentsAssigned->count();
                $data['teacher_students'] = $studentsAssigned;
            } else {
                $data['teacher_total_students'] = 0;
                $data['teacher_students'] = collect([]);
            }
            
            $data['upcoming_events'] = \App\Models\Event::where('event_date', '>=', date('Y-m-d'))
                ->orderBy('event_date', 'asc')
                ->limit(5)
                ->get();

            $data['calendar_events'] = \App\Models\Event::whereBetween('event_date', [date('Y-m-01'), date('Y-m-t')])
                ->orderBy('event_date', 'asc')
                ->get();

            // Fetch active/scheduled live video sessions hosted by this teacher
            $data['active_live_sessions'] = \App\Models\LearnhubLiveSession::where('teacher_id', $user->id)
                ->whereIn('status', ['live', 'scheduled'])
                ->with(['subject', 'lesson'])
                ->orderByDesc('status')
                ->orderBy('scheduled_at')
                ->get();
            
            return view('admin.teacher_index', $data);
        }

        if ($user->role === 'Parent' || $user->hasRole('Parent')) {
            return redirect()->route('parent.dashboard');
        }

        // Admin Dashboard Logic
        $data['total_student'] = User::where('usertype', 'student')->count();
        $data['total_teacher'] = User::where('usertype', 'Employee')->count();
        $data['total_parent'] = User::where('usertype', 'Parent')->count();
        
        // Calculate attendance percentage for today
        $date = date('Y-m-d');
        $total_present = EmployeeAttendance::where('date', $date)->where('attend_status', 'Present')->count();
        $total_attendance_records = EmployeeAttendance::where('date', $date)->count();
        
        if ($total_attendance_records > 0) {
            $data['attendance_today'] = round(($total_present / $total_attendance_records) * 100, 2);
        } else {
            $data['attendance_today'] = 0;
        }

        // Fetch new arrivals (latest 5 students)
        $data['recent_students'] = User::where('usertype', 'Student')->latest()->limit(5)->get();

        $data['upcoming_events'] = \App\Models\Event::where('event_date', '>=', date('Y-m-d'))
            ->orderBy('event_date', 'asc')
            ->limit(5)
            ->get();

        $data['calendar_events'] = \App\Models\Event::whereBetween('event_date', [date('Y-m-01'), date('Y-m-t')])
            ->orderBy('event_date', 'asc')
            ->get();

        // Fetch active/scheduled live sessions for students based on enrollment or show all for admin
        if ($user->hasRole('Student')) {
            $enrollments = \DB::table('student_section')
                ->where('student_id', $user->id)
                ->where('is_active', true)
                ->get();
            $studentClassIds = $enrollments->pluck('class_id')->unique()->filter();
            $studentYearIds = $enrollments->pluck('year_id')->unique()->filter();

            $subjectIds = \App\Models\LearnhubSubject::where(function ($q) use ($studentClassIds) {
                    $q->whereNull('class_id');
                    if ($studentClassIds->isNotEmpty()) {
                        $q->orWhereIn('class_id', $studentClassIds);
                    }
                })
                ->where(function ($q) use ($studentYearIds) {
                    $q->whereNull('year_id');
                    if ($studentYearIds->isNotEmpty()) {
                        $q->orWhereIn('year_id', $studentYearIds);
                    }
                })
                ->pluck('id');

            $data['active_live_sessions'] = \App\Models\LearnhubLiveSession::whereIn('subject_id', $subjectIds)
                ->whereIn('status', ['live', 'scheduled'])
                ->with(['subject', 'teacher', 'lesson'])
                ->orderByDesc('status')
                ->orderBy('scheduled_at')
                ->get();
        } else {
            // Admin sees all
            $data['active_live_sessions'] = \App\Models\LearnhubLiveSession::whereIn('status', ['live', 'scheduled'])
                ->with(['subject', 'teacher', 'lesson'])
                ->orderByDesc('status')
                ->orderBy('scheduled_at')
                ->get();
        }

        return view('admin.index', $data);
    }
}
 
