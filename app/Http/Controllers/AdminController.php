<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

            // Calculate teacher's total subjects and classes taking
            $teacherAssignments = \App\Models\TeacherAssignment::where('teacher_id', $user->id)->get();
            $assignSubjects = \App\Models\AssignSubject::where('teacher_id', $user->id)->get();

            $teacherClassIds = $teacherAssignments->pluck('class_id')->concat($assignSubjects->pluck('class_id'))->filter()->unique();
            $teacherSubjectIds = $teacherAssignments->pluck('subject_id')->concat($assignSubjects->pluck('subject_id'))->filter()->unique();

            $data['teacher_total_classes'] = $teacherClassIds->count();
            $data['teacher_total_subjects'] = $teacherSubjectIds->count();
            
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
        $data['total_student'] = $this->safeCount(function () {
            return User::whereIn(DB::raw('LOWER(usertype)'), ['student'])->count();
        });
        $data['total_teacher'] = $this->safeCount(function () {
            return User::whereIn(DB::raw('LOWER(usertype)'), ['employee', 'teacher'])
                ->orWhereIn(DB::raw('LOWER(role)'), ['teacher'])
                ->count();
        });
        $data['total_parent'] = $this->safeCount(function () {
            return User::whereIn(DB::raw('LOWER(usertype)'), ['parent'])->count();
        });
        $data['total_staff'] = $this->safeCount(function () {
            return User::whereIn(DB::raw('LOWER(usertype)'), ['employee', 'staff'])
                ->orWhereIn(DB::raw('LOWER(role)'), ['staff'])
                ->count();
        });
        $data['total_classes'] = $this->safeCount(function () {
            return \App\Models\StudentClass::count();
        });
        $data['fees_collected'] = $this->safeCurrency($this->safeSum(function () {
            if (Schema::hasTable('fee_payments')) {
                return \App\Models\FeePayment::sum('amount_paid');
            }

            if (Schema::hasTable('payments')) {
                return \App\Models\Payment::where('status', 'success')->sum('amount');
            }

            return 0;
        }));
        $data['pending_admissions'] = $this->safeCount(function () {
            return Schema::hasTable('admissions') ? DB::table('admissions')->where('status', 'pending')->count() : 0;
        });
        $data['upcoming_events_count'] = $this->safeCount(function () {
            return \App\Models\Event::whereBetween('event_date', [Carbon::today(), Carbon::today()->addDays(30)])->count();
        });
        $data['library_books_issued'] = $this->safeCount(function () {
            return Schema::hasTable('book_issues') ? DB::table('book_issues')->whereNull('returned_at')->count() : 0;
        });
        
        $attendanceSummary = $this->safeAttendanceSummary();
        $data['attendance_today'] = $attendanceSummary['percentage'];
        $data['attendance_present_today'] = $attendanceSummary['present'];
        $data['attendance_records_today'] = $attendanceSummary['total'];

        // Fetch new arrivals (latest 5 students)
        $data['recent_students'] = User::where('usertype', 'Student')->latest()->limit(5)->get();
        $data['class_distribution'] = $this->safeClassDistribution();
        $data['performance_distribution'] = $this->safePerformanceDistribution();
        $data['attendance_distribution'] = $this->safeAttendanceDistribution();
        $data['fee_status_distribution'] = $this->safeFeeStatusDistribution();

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

    private function safeCount(callable $query): int
    {
        try {
            return (int) $query();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeSum(callable $query): float
    {
        try {
            return (float) $query();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeCurrency(float $amount): string
    {
        return '₦' . number_format($amount, 2);
    }

    private function safeClassDistribution()
    {
        try {
            return \App\Models\StudentClass::leftJoin('assign_students', 'student_classes.id', '=', 'assign_students.class_id')
                ->select('student_classes.name', DB::raw('COUNT(assign_students.student_id) as students_count'))
                ->groupBy('student_classes.id', 'student_classes.name')
                ->orderBy('student_classes.name')
                ->pluck('students_count', 'name');
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function safePerformanceDistribution()
    {
        try {
            if (!Schema::hasTable('student_marks')) {
                return collect();
            }

            $scoreColumn = Schema::hasColumn('student_marks', 'total_score') ? 'total_score' : 'marks';

            return \App\Models\StudentClass::leftJoin('student_marks', 'student_classes.id', '=', 'student_marks.class_id')
                ->select('student_classes.name', DB::raw("ROUND(AVG(student_marks.{$scoreColumn}), 1) as average_score"))
                ->groupBy('student_classes.id', 'student_classes.name')
                ->orderBy('student_classes.name')
                ->pluck('average_score', 'name')
                ->map(fn ($score) => round((float) $score, 1));
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function safeAttendanceDistribution()
    {
        try {
            if (!Schema::hasTable('student_attendances')) {
                return collect();
            }

            return \App\Models\StudentAttendance::whereDate('date', Carbon::today())
                ->select('attend_status', DB::raw('COUNT(*) as total'))
                ->groupBy('attend_status')
                ->pluck('total', 'attend_status');
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function safeAttendanceSummary(): array
    {
        try {
            if (!Schema::hasTable('student_attendances')) {
                return ['percentage' => 0, 'present' => 0, 'total' => 0];
            }

            $today = Carbon::today();
            $present = \App\Models\StudentAttendance::whereDate('date', $today)
                ->where('attend_status', 'Present')
                ->count();
            $total = \App\Models\StudentAttendance::whereDate('date', $today)->count();

            return [
                'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
                'present' => $present,
                'total' => $total,
            ];
        } catch (\Throwable $e) {
            return ['percentage' => 0, 'present' => 0, 'total' => 0];
        }
    }

    private function safeFeeStatusDistribution()
    {
        try {
            if (!Schema::hasTable('student_fees')) {
                return collect();
            }

            return collect([
                'Paid' => DB::table('student_fees')->where('balance', '<=', 0)->count(),
                'Partial' => DB::table('student_fees')->where('total_paid', '>', 0)->where('balance', '>', 0)->count(),
                'Unpaid' => DB::table('student_fees')->where('total_paid', '<=', 0)->where('balance', '>', 0)->count(),
            ]);
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
 
