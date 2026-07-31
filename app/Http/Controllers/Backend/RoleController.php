<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function RoleView()
    {
        $data['allData'] = Role::all();
        return view('backend.role.view_role', $data);
    }

    public function RoleAdd()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('backend.role.add_role', compact('permissions'));
    }

    public function RoleStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->permissions) {
            $role->permissions()->attach($request->permissions);
        }

        $notification = array(
            'message' => 'Role Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('role.view')->with($notification);
    }

    public function RoleEdit($id)
    {
        $editData = Role::findOrFail($id);
        $permissions = Permission::all()->groupBy('module');
        return view('backend.role.edit_role', compact('editData', 'permissions'));
    }

    public function RoleUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions);

        $notification = array(
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('role.view')->with($notification);
    }

    public function RoleDelete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        $notification = array(
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'info'
        );

        return redirect()->route('role.view')->with($notification);
    }
}
