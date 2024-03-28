<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagement extends Controller
{
  public function index()
  {
    $users = User::where('activation', 'active')->with('role')->get();
    $roles = role::all();
    return view('content.user.user', compact('users', 'roles'));
  }

  public function changeRole(Request $request, $users)
  {
    $request->validate([
      'role_id' => 'required|exists:roles,id'
    ]);

    $user = User::findOrFail($users);
    $user->role_id = $request->input('role_id');
    $user->save();

    return redirect()->back()->with('success', 'Role changed successfully');
  }
}
