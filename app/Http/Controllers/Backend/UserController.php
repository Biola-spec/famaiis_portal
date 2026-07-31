<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function UserView(){
    	$data['allData'] = User::with('roles')->paginate(25);
    	return view('backend.user.view_user',$data);
    }


    public function UserAdd(){
        $roles = Role::all();
    	return view('backend.user.add_user', compact('roles'));
    }


    public function UserStore(Request $request){

    	$validatedData = $request->validate([
    		'email' => 'required|unique:users',
    		'name' => 'required',
    	]);

    	$data = new User();
    	$data->usertype = 'Admin';
        $data->role = $request->role_name; // Keep for legacy
    	$data->name = $request->name;
    	$data->email = $request->email;
    	$data->password = bcrypt($request->password);
        $data->code = $request->password;
        $data->status = 1;
    	$data->save();

        if ($request->roles) {
            $data->roles()->attach($request->roles);
        }

    	$notification = array(
    		'message' => 'User Inserted Successfully',
    		'alert-type' => 'success'
    	);

    	return redirect()->route('users.view')->with($notification);

    }



    public function UserEdit($id){
    	$editData = User::find($id);
        $roles = Role::all();
    	return view('backend.user.edit_user',compact('editData', 'roles'));

    }



    public function UserUpdate(Request $request, $id){

    	$data = User::find($id);
    	$data->name = $request->name;
    	$data->email = $request->email;
        $data->role = $request->role_name; // Keep for legacy
        if ($request->password) {
            $data->password = bcrypt($request->password);
            $data->code = $request->password;
        }
    	$data->save();

        $data->roles()->sync($request->roles);

    	$notification = array(
    		'message' => 'User Updated Successfully',
    		'alert-type' => 'info'
    	);

    	return redirect()->route('users.view')->with($notification);

    }



    public function UserDelete($id){
    	$user = User::find($id);
    	$user->delete();

    	$notification = array(
    		'message' => 'User Deleted Successfully',
    		'alert-type' => 'info'
    	);

    	return redirect()->route('users.view')->with($notification);

    }





}
 