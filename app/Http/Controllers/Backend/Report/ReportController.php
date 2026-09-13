<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\AssignStudent;
use App\Models\Report;
use App\Models\ReportMedia;
use App\Models\SchoolSection;
use App\Models\SchoolSubject;
use App\Models\StudentClass;
use App\Models\StudentSection;
use App\Models\StudentYear;
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
        $reports = Report::with(['studentClass', 'subject', 'students'])
            ->where('teacher_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.report.teacher.index', compact('reports'));
    }

    public function teacherCreate()
    {
        $classes = $this->teacherClasses(Auth::user());

        return view('backend.report.teacher.create', compact('classes'));
    }

    public function teacherEdit($id)
    {
        $report = Report::with(['students', 'media', 'subject'])->findOrFail($id);
        abort_unless($this->canTeacherManageReport($report), 403);

        if (!$this->isAdminUser(Auth::user()) && ($report->status ?? 'pending') === 'approved') {
            return redirect()->route('teacher.report.index')->with([
                'message' => 'Recall the approved report before making amendments.',
                'alert-type' => 'warning',
            ]);
        }

        $classes = $this->teacherClasses(Auth::user());

        return view('backend.report.teacher.create', compact('classes', 'report'));
    }

    public function show($id)
    {
        $report = Report::with(['teacher', 'studentClass', 'subject', 'students', 'media', 'approvedBy', 'recalledBy'])
            ->findOrFail($id);

        abort_unless($this->canViewReportInStaffArea($report), 403);

        return view('backend.report.teacher.show', compact('report'));
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
                ->filter()
                ->values();
        }
        return response()->json($subjects);
    }

    public function getTeacherStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer',
        ]);

        return response()->json($this->getClassStudents($request->integer('class_id'))->values());
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
            $report->status = 'pending';

            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('reports/videos', 'public');
                $report->video_path = $videoPath;
            }

            $report->save();

            // Link Students
            if ($report->is_for_all) {
                $report->students()->attach(
                    $this->getClassStudents((int) $request->class_id)->pluck('id')->all()
                );
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

            $teacher = Auth::user();
            $reviewers = $this->reportReviewers($report);

            if ($reviewers->isNotEmpty()) {
                Notification::send($reviewers, new ReportNotification([
                    'report_id' => $report->id,
                    'title' => 'Activity report awaiting approval: ' . $report->title,
                    'message' => 'A new report has been submitted by ' . $teacher->name . ' and needs approval before students and parents can see it.',
                    'type' => 'report_approval',
                ]));
            }
        });

        return redirect()->route('teacher.report.index')->with([
            'message' => 'Report submitted successfully and is waiting for approval before students and parents can see it.',
            'alert-type' => 'info',
        ]);
    }

    public function teacherUpdate(Request $request, $id)
    {
        $report = Report::with(['students', 'media'])->findOrFail($id);
        abort_unless($this->canTeacherManageReport($report), 403);

        $request->validate([
            'class_id' => 'required',
            'report_type' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'documents.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'target' => 'required',
            'student_ids' => 'required_if:target,specific|array',
        ]);

        DB::transaction(function () use ($request, $report) {
            $report->class_id = $request->class_id;
            $report->subject_id = $request->subject_id;
            $report->report_type = $request->report_type;
            $report->title = $request->title;
            $report->description = $request->description;
            $report->is_for_all = ($request->target == 'all');
            $report->status = 'pending';
            $report->approved_by = null;
            $report->approved_at = null;
            $report->recalled_by = null;
            $report->recalled_at = null;

            if ($request->hasFile('video')) {
                if ($report->video_path) {
                    Storage::disk('public')->delete($report->video_path);
                }
                $report->video_path = $request->file('video')->store('reports/videos', 'public');
            }

            $report->save();

            if ($report->is_for_all) {
                $report->students()->sync(
                    $this->getClassStudents((int) $request->class_id)->pluck('id')->all()
                );
            } else {
                $report->students()->sync($request->student_ids);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    ReportMedia::create([
                        'report_id' => $report->id,
                        'file_path' => $file->store('reports/images', 'public'),
                        'file_type' => 'image'
                    ]);
                }
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    ReportMedia::create([
                        'report_id' => $report->id,
                        'file_path' => $file->store('reports/documents', 'public'),
                        'file_type' => 'document'
                    ]);
                }
            }

            $teacher = Auth::user();
            $reviewers = $this->reportReviewers($report);

            if ($reviewers->isNotEmpty()) {
                Notification::send($reviewers, new ReportNotification([
                    'report_id' => $report->id,
                    'title' => 'Activity report updated and awaiting approval: ' . $report->title,
                    'message' => 'A report has been updated by ' . $teacher->name . ' and needs approval before students and parents can see it.',
                    'type' => 'report_approval',
                ]));
            }
        });

        return redirect()->route($this->isAdminUser(Auth::user()) ? 'admin.report.index' : 'teacher.report.index')->with([
            'message' => 'Report updated successfully and sent back for approval.',
            'alert-type' => 'info',
        ]);
    }

    public function teacherRecall($id)
    {
        $report = Report::findOrFail($id);
        abort_unless((int) $report->teacher_id === (int) Auth::id(), 403);

        if (($report->status ?? 'pending') !== 'approved') {
            return redirect()->back()->with([
                'message' => 'Only approved reports can be recalled for amendment.',
                'alert-type' => 'warning',
            ]);
        }

        $report->update([
            'status' => 'recalled',
            'recalled_by' => Auth::id(),
            'recalled_at' => now(),
        ]);

        return redirect()->route('teacher.report.edit', $report->id)->with([
            'message' => 'Report recalled. Make your amendment and submit it again for approval.',
            'alert-type' => 'info',
        ]);
    }

    private function getClassStudents(int $classId)
    {
        $activeYear = getCurrentSession() ?? StudentYear::where('is_active', 1)->first() ?? StudentYear::first();
        $yearId = $activeYear?->id;

        $students = collect();

        $assignQuery = AssignStudent::with('student')
            ->whereHas('student')
            ->where('class_id', $classId);

        if ($yearId) {
            $assignQuery->where('year_id', $yearId);
        }

        $this->addStudentsToCollection($students, $assignQuery->get()->pluck('student'));

        $sectionQuery = StudentSection::with('student')
            ->whereHas('student')
            ->where('class_id', $classId)
            ->where('is_active', true);

        if ($yearId) {
            $sectionQuery->where('year_id', $yearId);
        }

        $this->addStudentsToCollection($students, $sectionQuery->get()->pluck('student'));

        if ($students->isEmpty()) {
            $this->addStudentsToCollection(
                $students,
                AssignStudent::with('student')
                    ->whereHas('student')
                    ->where('class_id', $classId)
                    ->get()
                    ->pluck('student')
            );

            $this->addStudentsToCollection(
                $students,
                StudentSection::with('student')
                    ->whereHas('student')
                    ->where('class_id', $classId)
                    ->get()
                    ->pluck('student')
            );
        }

        return $students->sortBy('name')->values();
    }

    private function teacherClasses(?User $user)
    {
        if ($this->isAdminUser($user)) {
            return StudentClass::orderBy('name')->get();
        }

        $assignmentClassIds = TeacherAssignment::where('teacher_id', $user->id)->pluck('class_id');
        $classTeacherClassIds = \App\Models\AssignClassTeacher::where('teacher_id', $user->id)->pluck('class_id');
        $classIds = $assignmentClassIds->merge($classTeacherClassIds)->unique()->filter();

        return StudentClass::whereIn('id', $classIds)->orderBy('name')->get();
    }

    private function canTeacherManageReport(Report $report): bool
    {
        return $this->isAdminUser(Auth::user()) || (int) $report->teacher_id === (int) Auth::id();
    }

    private function canViewReportInStaffArea(Report $report): bool
    {
        return $this->canTeacherManageReport($report) || $this->canModerateReport($report);
    }

    private function addStudentsToCollection($students, $studentRecords): void
    {
        foreach ($studentRecords as $student) {
            if (!$student || $students->has($student->id)) {
                continue;
            }

            $name = $student->name;
            if (empty($name)) {
                $name = trim(implode(' ', array_filter([
                    $student->first_name ?? null,
                    $student->surname ?? null,
                    $student->middle_name ?? null,
                ])));
            }
            if (empty($name)) {
                $name = trim(implode(' ', array_filter([
                    $student->fname ?? null,
                    $student->surname ?? null,
                    $student->mname ?? null,
                ])));
            }
            if (empty($name)) {
                $name = $student->email ?? ('Student #' . $student->id);
            }

            $students->put($student->id, [
                'id' => $student->id,
                'student_id' => $student->id,
                'name' => $name,
                'id_no' => $student->id_no ?? '',
            ]);
        }
    }

    // --- Parent Actions ---

    public function parentIndex(Request $request)
    {
        $user = Auth::user();
        $childIds = $user->children()->pluck('student_id');

        $query = Report::with(['teacher', 'studentClass', 'subject', 'media', 'students'])
            ->where('status', 'approved')
            ->whereHas('students', function ($q) use ($childIds) {
                $q->whereIn('student_id', $childIds);
            });

        if ($request->report_type) {
            $query->where('report_type', $request->report_type);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $reports = $query->orderByDesc('approved_at')->orderByDesc('id')->paginate(10);

        return view('backend.report.parent.index', compact('reports'));
    }

    public function studentIndex(Request $request)
    {
        $studentId = Auth::id();

        $query = Report::with(['teacher', 'studentClass', 'subject', 'media', 'students'])
            ->where('status', 'approved')
            ->whereHas('students', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            });

        if ($request->report_type) {
            $query->where('report_type', $request->report_type);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $reports = $query->orderByDesc('approved_at')->orderByDesc('id')->paginate(10);

        return view('backend.report.student.index', compact('reports'));
    }

    public function markAsSeen($id)
    {
        $report = Report::where('status', 'approved')->findOrFail($id);
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
        $query = Report::with(['teacher', 'studentClass', 'subject', 'approvedBy', 'recalledBy', 'students']);
        $user = Auth::user();

        if (!$this->isAdminUser($user)) {
            $headClassIds = $this->headSectionClassIds($user);
            abort_if($headClassIds->isEmpty(), 403);
            $query->whereIn('class_id', $headClassIds);
        }

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }
        if ($request->report_type) {
            $query->where('report_type', $request->report_type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('id', 'desc')->paginate(20);
        $classes = $this->isAdminUser($user)
            ? StudentClass::all()
            : StudentClass::whereIn('id', $this->headSectionClassIds($user))->get();
        $teachers = User::whereIn('role', ['Teacher', 'Staff'])->get();

        return view('backend.report.admin.index', compact('reports', 'classes', 'teachers'));
    }

    public function approve($id)
    {
        $report = Report::with(['teacher', 'students'])->findOrFail($id);
        abort_unless($this->canModerateReport($report), 403);

        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'recalled_by' => null,
            'recalled_at' => null,
        ]);

        $this->notifyReportAudience($report);

        return redirect()->back()->with([
            'message' => 'Report approved and sent to students and parents.',
            'alert-type' => 'success',
        ]);
    }

    public function recall($id)
    {
        $report = Report::findOrFail($id);
        abort_unless($this->canModerateReport($report), 403);

        $report->update([
            'status' => 'recalled',
            'recalled_by' => Auth::id(),
            'recalled_at' => now(),
        ]);

        return redirect()->back()->with([
            'message' => 'Report recalled. Students and parents will no longer see it.',
            'alert-type' => 'warning',
        ]);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        abort_unless($this->canModerateReport($report), 403);
        
        // Delete files
        if ($report->video_path) {
            Storage::disk('public')->delete($report->video_path);
        }
        foreach ($report->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $report->delete();

        return redirect()->back()->with([
            'message' => 'Report deleted successfully.',
            'alert-type' => 'success',
        ]);
    }

    private function notifyReportAudience(Report $report): void
    {
        $report->loadMissing(['teacher', 'students']);
        $studentIds = $report->students->pluck('id');

        $students = User::whereIn('id', $studentIds)->get();
        $parents = User::whereHas('children', function ($q) use ($studentIds) {
            $q->whereIn('student_id', $studentIds);
        })->get();

        $notificationData = [
            'report_id' => $report->id,
            'title' => 'New ' . ucfirst($report->report_type) . ' Report: ' . $report->title,
            'message' => 'A new report has been posted by ' . optional($report->teacher)->name,
            'type' => 'report',
        ];

        Notification::send($students, new ReportNotification($notificationData));
        Notification::send($parents, new ReportNotification($notificationData));
    }

    private function reportReviewers(Report $report)
    {
        $admins = User::query()
            ->where(function ($query) {
                $query->where('role', 'Admin')->orWhere('usertype', 'Admin');
            })
            ->get();

        $headTeacherIds = SchoolSection::query()
            ->whereHas('classes', function ($query) use ($report) {
                $query->where('id', $report->class_id);
            })
            ->whereNotNull('head_teacher_id')
            ->pluck('head_teacher_id');

        $heads = User::whereIn('id', $headTeacherIds)->get();

        return $admins->merge($heads)->unique('id')->values();
    }

    private function canModerateReport(Report $report): bool
    {
        $user = Auth::user();

        if ($this->isAdminUser($user)) {
            return true;
        }

        return $this->headSectionClassIds($user)->contains($report->class_id);
    }

    private function isAdminUser(?User $user): bool
    {
        return $user && ($user->role === 'Admin' || $user->hasRole('Admin', 'Super Admin'));
    }

    private function headSectionClassIds(?User $user)
    {
        if (!$user) {
            return collect();
        }

        return StudentClass::query()
            ->whereIn('section_id', SchoolSection::where('head_teacher_id', $user->id)->pluck('id'))
            ->pluck('id');
    }
}
