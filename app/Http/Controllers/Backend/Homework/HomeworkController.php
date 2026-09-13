<?php

namespace App\Http\Controllers\Backend\Homework;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Homework;
use App\Models\StudentClass;
use App\Models\SchoolSubject;
use App\Models\AssignSubject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\AssignStudent;
use App\Models\SchoolSection;
use Auth;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function HomeworkView()
    {
        $user = Auth::user();
        $is_admin = ($user->role == 'Admin' || $user->hasRole('Admin'));
        $is_teacher = ($user->role == 'Teacher' || $user->hasRole('Teacher'));
        $is_student = ($user->role == 'Student' || $user->hasRole('Student'));
        $moderatedClassIds = StudentClass::query()
            ->whereIn('section_id', SchoolSection::where('head_teacher_id', $user->id)->pluck('id'))
            ->pluck('id');

        if ($is_admin) {
            $allData = Homework::with(['approvedBy', 'recalledBy'])->orderBy('id', 'desc')->get();
        } elseif ($is_teacher) {
            $allData = Homework::with(['approvedBy', 'recalledBy'])->where('teacher_id', $user->id)->orderBy('id', 'desc')->get();
        } elseif ($is_student) {
            $assignStudent = AssignStudent::where('student_id', $user->id)->first();
            if ($assignStudent) {
                $allData = Homework::where('class_id', $assignStudent->class_id)
                    ->where('status', 'approved')
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $allData = collect();
            }
        } else {
            $allData = collect();
        }

        return view('backend.homework.view_homework', compact('allData', 'moderatedClassIds'));
    }

    public function HomeworkAdd()
    {
        $user = Auth::user();
        abort_unless($this->canManageHomeworkContent($user), 403);
        $is_admin = ($user->role == 'Admin' || $user->hasRole('Admin'));

        if ($is_admin) {
            $data['classes'] = StudentClass::all();
            $data['subjects'] = SchoolSubject::all();
        } else {
            // For teachers, only show classes and subjects they are assigned to
            $assigned = TeacherAssignment::where('teacher_id', $user->id)->get();
            $data['classes'] = StudentClass::whereIn('id', $assigned->pluck('class_id'))->get();
            $data['subjects'] = SchoolSubject::whereIn('id', $assigned->pluck('subject_id'))->get();
        }

        return view('backend.homework.add_homework', $data);
    }

    public function HomeworkStore(Request $request)
    {
        abort_unless($this->canManageHomeworkContent(Auth::user()), 403);

        $request->validate([
            'title' => 'required',
            'class_id' => 'required',
            'subject_id' => 'required',
            'type' => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        $data = new Homework();
        $data->title = $request->title;
        $data->description = $request->description;
        $data->class_id = $request->class_id;
        $data->subject_id = $request->subject_id;
        $data->teacher_id = Auth::user()->id;
        $data->due_date = $request->due_date;
        $data->type = $request->type;
        $data->status = 'pending';

        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->storeAs('homeworks', $filename);
            $data->file = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Homework/Note Uploaded Successfully and waiting for approval before students can view it.',
            'alert-type' => 'info'
        );

        return redirect()->route('homework.view')->with($notification);
    }

    public function HomeworkEdit($id)
    {
        $editData = Homework::findOrFail($id);
        
        $user = Auth::user();
        abort_unless($this->canManageHomeworkContent($user), 403);
        $is_admin = ($user->role == 'Admin' || $user->hasRole('Admin'));
        
        if (!$is_admin && $editData->teacher_id != $user->id) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        if (!$is_admin && ($editData->status ?? 'pending') === 'approved') {
            return redirect()->route('homework.view')->with([
                'message' => 'Recall the approved homework/note before making amendments.',
                'alert-type' => 'warning',
            ]);
        }

        if ($is_admin) {
            $data['classes'] = StudentClass::all();
            $data['subjects'] = SchoolSubject::all();
        } else {
            $assigned = TeacherAssignment::where('teacher_id', $user->id)->get();
            $data['classes'] = StudentClass::whereIn('id', $assigned->pluck('class_id'))->get();
            $data['subjects'] = SchoolSubject::whereIn('id', $assigned->pluck('subject_id'))->get();
        }

        $data['editData'] = $editData;
        return view('backend.homework.edit_homework', $data);
    }

    public function HomeworkUpdate(Request $request, $id)
    {
        abort_unless($this->canManageHomeworkContent(Auth::user()), 403);

        $request->validate([
            'title' => 'required',
            'class_id' => 'required',
            'subject_id' => 'required',
            'type' => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        $data = Homework::findOrFail($id);
        $data->title = $request->title;
        $data->description = $request->description;
        $data->class_id = $request->class_id;
        $data->subject_id = $request->subject_id;
        $data->due_date = $request->due_date;
        $data->type = $request->type;
        $data->status = 'pending';
        $data->approved_by = null;
        $data->approved_at = null;
        $data->recalled_by = null;
        $data->recalled_at = null;

        if ($request->file('file')) {
            $file = $request->file('file');
            if ($data->file) {
                Storage::delete('homeworks/' . $data->file);
            }
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->storeAs('homeworks', $filename);
            $data->file = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Homework/Note Updated Successfully and sent back for approval.',
            'alert-type' => 'info'
        );

        return redirect()->route('homework.view')->with($notification);
    }

    public function HomeworkDelete($id)
    {
        abort_unless($this->canManageHomeworkContent(Auth::user()), 403);

        $data = Homework::findOrFail($id);
        if ($data->file) {
            Storage::delete('homeworks/' . $data->file);
        }
        $data->delete();

        $notification = array(
            'message' => 'Homework/Note Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('homework.view')->with($notification);
    }

    public function HomeworkApprove($id)
    {
        $data = Homework::findOrFail($id);
        abort_unless($this->canModerateHomework($data), 403);

        $data->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'recalled_by' => null,
            'recalled_at' => null,
        ]);

        return redirect()->back()->with([
            'message' => 'Homework/Note approved. Students can now view it.',
            'alert-type' => 'success',
        ]);
    }

    public function HomeworkRecall($id)
    {
        $data = Homework::findOrFail($id);
        abort_unless($this->canRecallHomework($data), 403);

        if (($data->status ?? 'pending') !== 'approved') {
            return redirect()->back()->with([
                'message' => 'Only approved homework/notes can be recalled.',
                'alert-type' => 'warning',
            ]);
        }

        $data->update([
            'status' => 'recalled',
            'recalled_by' => Auth::id(),
            'recalled_at' => now(),
        ]);

        return redirect()->route('homework.edit', $data->id)->with([
            'message' => 'Homework/Note recalled. Make the amendment and submit again for approval.',
            'alert-type' => 'info',
        ]);
    }

    public function HomeworkDownload($id)
    {
        $data = Homework::findOrFail($id);
        if ((Auth::user()->hasRole('Parent', 'Student') || in_array(Auth::user()->role, ['Parent', 'Student'])) && ($data->status ?? 'pending') !== 'approved') {
            abort(403);
        }
        if ($data->file) {
            return Storage::download('homeworks/' . $data->file);
        }
        return redirect()->back();
    }

    private function canModerateHomework(Homework $homework): bool
    {
        $user = Auth::user();

        if ($user->role === 'Admin' || $user->hasRole('Admin', 'Super Admin')) {
            return true;
        }

        return StudentClass::query()
            ->whereIn('section_id', SchoolSection::where('head_teacher_id', $user->id)->pluck('id'))
            ->pluck('id')
            ->contains($homework->class_id);
    }

    private function canRecallHomework(Homework $homework): bool
    {
        return $this->canModerateHomework($homework) || (int) $homework->teacher_id === (int) Auth::id();
    }

    private function canManageHomeworkContent(?User $user): bool
    {
        return $user && (
            $user->hasRole('Admin', 'Super Admin', 'Teacher', 'Staff') ||
            in_array($user->role, ['Admin', 'Super Admin', 'Teacher', 'Staff'])
        );
    }
}
