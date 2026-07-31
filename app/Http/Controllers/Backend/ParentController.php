<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class ParentController extends Controller
{
    public function ParentView()
    {
        $parentRole = Role::where('name', 'Parent')->first();
        $allData = $parentRole ? $parentRole->users : collect();
        return view('backend.parent.view_parent', compact('allData'));
    }

    public function ParentAdd()
    {
        $students = User::where('usertype', 'Student')->get();
        return view('backend.parent.add_parent', compact('students'));
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
        if ($request->student_id) {
            $data->children()->sync($request->student_id);
        }

        $notification = array(
            'message' => 'Parent Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('parent.view')->with($notification);
    }

    public function ParentEdit($id)
    {
        $editData = User::findOrFail($id);
        $students = User::where('usertype', 'Student')->get();
        return view('backend.parent.edit_parent', compact('editData', 'students'));
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

        if ($request->student_id) {
            $data->children()->sync($request->student_id);
        }

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
