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

        if ($is_admin) {
            $allData = Homework::orderBy('id', 'desc')->get();
        } elseif ($is_teacher) {
            $allData = Homework::where('teacher_id', $user->id)->orderBy('id', 'desc')->get();
        } elseif ($is_student) {
            $assignStudent = AssignStudent::where('student_id', $user->id)->first();
            if ($assignStudent) {
                $allData = Homework::where('class_id', $assignStudent->class_id)->orderBy('id', 'desc')->get();
            } else {
                $allData = collect();
            }
        } else {
            $allData = collect();
        }

        return view('backend.homework.view_homework', compact('allData'));
    }

    public function HomeworkAdd()
    {
        $user = Auth::user();
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

        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->storeAs('homeworks', $filename);
            $data->file = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Homework/Note Uploaded Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('homework.view')->with($notification);
    }

    public function HomeworkEdit($id)
    {
        $editData = Homework::findOrFail($id);
        
        $user = Auth::user();
        $is_admin = ($user->role == 'Admin' || $user->hasRole('Admin'));
        
        if (!$is_admin && $editData->teacher_id != $user->id) {
            return redirect()->back()->with('error', 'Unauthorized access');
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
            'message' => 'Homework/Note Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('homework.view')->with($notification);
    }

    public function HomeworkDelete($id)
    {
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

    public function HomeworkDownload($id)
    {
        $data = Homework::findOrFail($id);
        if ($data->file) {
            return Storage::download('homeworks/' . $data->file);
        }
        return redirect()->back();
    }
}
