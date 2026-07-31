<?php

namespace App\Http\Controllers\Backend\Homework;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Homework;
use App\Models\Submission;
use Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function SubmissionStore(Request $request)
    {
        $request->validate([
            'homework_id' => 'required',
            'answer' => 'nullable',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        // Check if already submitted
        $existing = Submission::where('homework_id', $request->homework_id)
                             ->where('student_id', Auth::user()->id)
                             ->first();

        if ($existing) {
            $data = $existing;
        } else {
            $data = new Submission();
        }

        $data->homework_id = $request->homework_id;
        $data->student_id = Auth::user()->id;
        $data->answer = $request->answer;

        if ($request->file('file')) {
            $file = $request->file('file');
            if ($data->file) {
                Storage::delete('submissions/' . $data->file);
            }
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->storeAs('submissions', $filename);
            $data->file = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Homework Submitted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function SubmissionView($homework_id)
    {
        $homework = Homework::findOrFail($homework_id);
        
        $query = Submission::where('homework_id', $homework_id);
        $user = Auth::user();

        if ($user->role == 'Student' || $user->hasRole('Student')) {
            $query->where('student_id', $user->id);
        } elseif ($user->hasRole('Parent')) {
            $childIds = $user->children->pluck('id')->toArray();
            $query->whereIn('student_id', $childIds);
        }
        
        $submissions = $query->get();
        
        return view('backend.homework.submission_list', compact('homework', 'submissions'));
    }

    public function SubmissionDownload($id)
    {
        $data = Submission::findOrFail($id);
        if ($data->file) {
            return Storage::download('submissions/' . $data->file);
        }
        return redirect()->back();
    }
}
