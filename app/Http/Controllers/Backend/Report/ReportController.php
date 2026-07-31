<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\AssignStudent;
use App\Models\Report;
use App\Models\ReportMedia;
use App\Models\SchoolSubject;
use App\Models\StudentClass;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Notifications\ReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // --- Teacher Actions ---

    public function teacherIndex()
    {
        $reports = Report::with(['studentClass', 'subject'])
            ->where('teacher_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.report.teacher.index', compact('reports'));
    }

    public function teacherCreate()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin') || $user->role === 'Admin') {
            $classes = StudentClass::orderBy('name')->get();
        } else {
            $classIds = TeacherAssignment::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();
            $classes = StudentClass::whereIn('id', $classIds)->orderBy('name')->get();
        }

        return view('backend.report.teacher.create', compact('classes'));
    }

    public function getTeacherSubjects(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('Admin') || $user->role === 'Admin') {
            $subjects = SchoolSubject::orderBy('name')->get();
        } else {
            $subjects = TeacherAssignment::with('subject')
                ->where('teacher_id', $user->id)
                ->where('class_id', $request->class_id)
                ->get()
                ->pluck('subject')
                ->filter();
        }
        return response()->json($subjects);
    }

    public function getTeacherStudents(Request $request)
    {
        $students = AssignStudent::with('student')
            ->where('class_id', $request->class_id)
            ->where('year_id', getCurrentSession()->id ?? 0)
            ->get()
            ->pluck('student');

        return response()->json($students);
    }

    public function teacherStore(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'report_type' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480', // 20MB
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'documents.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'target' => 'required', // 'all' or 'specific'
            'student_ids' => 'required_if:target,specific|array',
        ]);

        DB::transaction(function () use ($request) {
            $report = new Report();
            $report->teacher_id = Auth::id();
            $report->class_id = $request->class_id;
            $report->subject_id = $request->subject_id;
            $report->report_type = $request->report_type;
            $report->title = $request->title;
            $report->description = $request->description;
            $report->is_for_all = ($request->target == 'all');

            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('reports/videos', 'public');
                $report->video_path = $videoPath;
            }

            $report->save();

            // Link Students
            if ($report->is_for_all) {
                $studentIds = AssignStudent::where('class_id', $request->class_id)
                    ->where('year_id', getCurrentSession()->id ?? 0)
                    ->pluck('student_id')
                    ->toArray();
                $report->students()->attach($studentIds);
            } else {
                $report->students()->attach($request->student_ids);
            }

            // Handle Media
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('reports/images', 'public');
                    ReportMedia::create([
                        'report_id' => $report->id,
                        'file_path' => $path,
                        'file_type' => 'image'
                    ]);
                }
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('reports/documents', 'public');
                    ReportMedia::create([
                        'report_id' => $report->id,
                        'file_path' => $path,
                        'file_type' => 'document'
                    ]);
                }
            }

            // Notifications
            $parents = User::whereHas('children', function ($q) use ($report) {
                $q->whereIn('student_id', $report->students()->pluck('student_id'));
            })->get();

            $admins = User::where('role', 'Admin')->get();
            $teacher = Auth::user();

            $notificationData = [
                'report_id' => $report->id,
                'title' => 'New ' . ucfirst($report->report_type) . ' Report: ' . $report->title,
                'message' => 'A new report has been posted by ' . $teacher->name,
                'type' => 'report'
            ];

            Notification::send($parents, new ReportNotification($notificationData));
            Notification::send($admins, new ReportNotification($notificationData));
            $teacher->notify(new ReportNotification(array_merge($notificationData, ['title' => 'Report Published: ' . $report->title, 'message' => 'Your report has been successfully published.'])));
        });

        return redirect()->route('teacher.report.index')->with('success', 'Report created successfully.');
    }

    // --- Parent Actions ---

    public function parentIndex(Request $request)
    {
        $user = Auth::user();
        $childIds = $user->children()->pluck('student_id');

        $query = Report::with(['teacher', 'studentClass', 'subject', 'media', 'students'])
            ->whereHas('students', function ($q) use ($childIds) {
                $q->whereIn('student_id', $childIds);
            });

        if ($request->report_type) {
            $query->where('report_type', $request->report_type);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $reports = $query->orderBy('id', 'desc')->paginate(10);

        return view('backend.report.parent.index', compact('reports'));
    }

    public function markAsSeen($id)
    {
        $report = Report::findOrFail($id);
        $user = Auth::user();
        $childIds = $user->children()->pluck('student_id');

        foreach ($childIds as $studentId) {
            $report->students()->updateExistingPivot($studentId, ['seen_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // --- Admin Actions ---

    public function adminIndex(Request $request)
    {
        $query = Report::with(['teacher', 'studentClass', 'subject']);

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->report_type) {
            $query->where('report_type', $request->report_type);
        }

        $reports = $query->orderBy('id', 'desc')->paginate(20);
        $classes = StudentClass::all();
        $teachers = User::whereIn('role', ['Teacher', 'Staff'])->get();

        return view('backend.report.admin.index', compact('reports', 'classes', 'teachers'));
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        // Delete files
        if ($report->video_path) {
            Storage::disk('public')->delete($report->video_path);
        }
        foreach ($report->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $report->delete();

        return redirect()->back()->with('success', 'Report deleted successfully.');
    }
}
