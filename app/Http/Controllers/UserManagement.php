<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;

class UserManagement extends Controller
{
  public function index()
  {
    $users = User::where('activation', 'active')->with('role')->get();
    $roles = role::all();
    return view('content.user.user', compact('users', 'roles'));
  }
}
