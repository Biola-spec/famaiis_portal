<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ParentResultLink;
use App\Models\StudentYear;

class ParentController extends Controller
{
    public function ParentView()
    {
        $parentRole = Role::where('name', 'Parent')->first();
        $allData = $parentRole ? $parentRole->users()->with(['children', 'resultLinks' => function ($query) {
            $query->where('is_active', true);
        }])->get() : collect();
        return view('backend.parent.view_parent', compact('allData'));
    }

    public function ParentAdd()
    {
        $students = User::where('usertype', 'Student')
            ->with(['studentClassAssignments.student_class'])
            ->get();

        foreach ($students as $student) {
            $latestAssignment = $student->studentClassAssignments->sortByDesc('id')->first();
            $student->class_name = $latestAssignment && $latestAssignment->student_class 
                ? $latestAssignment->student_class->name 
                : 'No Assigned Class';
        }

        $students = $students->sortBy([
            ['class_name', 'asc'],
            ['name', 'asc']
        ]);

        $groupedStudents = $students->groupBy('class_name');

        if ($groupedStudents->has('No Assigned Class')) {
            $noClassGroup = $groupedStudents->pull('No Assigned Class');
            $groupedStudents->put('No Assigned Class', $noClassGroup);
        }

        return view('backend.parent.add_parent', compact('groupedStudents'));
    }

    public function ParentStore(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|unique:users',
            'name' => 'required',
        ]);

        $data = new User();
        $data->usertype = 'Parent';
        $data->role = 'Parent';
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->code = $request->password;
        $data->status = 1;

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/parent_images'),$filename);
            $data['image'] = $filename;
        }

        $data->save();

        // Assign Parent role
        $parentRole = Role::where('name', 'Parent')->first();
        if ($parentRole) {
            $data->roles()->attach($parentRole->id);
        }

        // Link child
        $data->children()->sync($request->student_id ?? []);

        $notification = array(
            'message' => 'Parent Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('parent.view')->with($notification);
    }

    public function ParentEdit($id)
    {
        $editData = User::with('children.studentClassAssignments.student_class')->findOrFail($id);
        $students = User::where('usertype', 'Student')
            ->with(['studentClassAssignments.student_class'])
            ->get();

        foreach ($students as $student) {
            $latestAssignment = $student->studentClassAssignments->sortByDesc('id')->first();
            $student->class_name = $latestAssignment && $latestAssignment->student_class 
                ? $latestAssignment->student_class->name 
                : 'No Assigned Class';
        }

        $students = $students->sortBy([
            ['class_name', 'asc'],
            ['name', 'asc']
        ]);

        $groupedStudents = $students->groupBy('class_name');

        if ($groupedStudents->has('No Assigned Class')) {
            $noClassGroup = $groupedStudents->pull('No Assigned Class');
            $groupedStudents->put('No Assigned Class', $noClassGroup);
        }

        $activeLinks = ParentResultLink::query()
            ->where('parent_id', $editData->id)
            ->where('is_active', true)
            ->latest()
            ->get();
        $sessions = StudentYear::orderByDesc('id')->get();

        return view('backend.parent.edit_parent', compact('editData', 'groupedStudents', 'activeLinks', 'sessions'));
    }

    public function ParentUpdate(Request $request, $id)
    {
        $data = User::findOrFail($id);
        $data->name = $request->name;
        $data->email = $request->email;
        if ($request->password) {
            $data->password = bcrypt($request->password);
            $data->code = $request->password;
        }

        if ($request->file('image')) {
            $file = $request->file('image');
            @unlink(public_path('upload/parent_images/'.$data->image));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/parent_images'),$filename);
            $data['image'] = $filename;
        }
        $data->save();

        $data->children()->sync($request->student_id ?? []);

        $notification = array(
            'message' => 'Parent Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('parent.view')->with($notification);
    }

    public function ParentDelete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $notification = array(
            'message' => 'Parent Deleted Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('parent.view')->with($notification);
    }
}
