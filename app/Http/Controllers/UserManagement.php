<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notificationType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Add missing import statement

class UserManagement extends Controller
{
  public function index()
  {
    $users = User::where('activation', 'active')->with('role')->get();
    $roles = role::all();
    return view('content.user.user', compact('users', 'roles'));
  }

  public function manageUser()
  {
    $users = User::where('activation', 'active')->with('role')->get();
    $roles = role::all();
    $notifTypes = notificationType::all();
    return view('content.user.manageUser', compact('users', 'roles', 'notifTypes'));
  }

  public function sendNotification(Request $request)
  {
    $notificationData = [
      'title' => $request->input('title'),
      'message' => $request->input('message')
    ];

    $apiUrl = 'https://api.example.com/send-notification';
    $apiKey = 'your-api-key';

    $client = new \GuzzleHttp\Client();
    try {
      $response = $client->post($apiUrl, [
        'headers' => [
          'Authorization' => 'Bearer ' . $apiKey,
          'Content-Type' => 'application/json',
        ],
        'json' => $notificationData
      ]);

      if ($response->getStatusCode() == 200) {
        return redirect()->back()->with('success', 'Notification sent successfully');
      } else {
        return redirect()->back()->with('error', 'Failed to send notification');
      }
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Failed to send notification: ' . $e->getMessage());
    }
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

  public function adminviwLogin()
  {
    return view('content.authentications.auth-login-basic');
  }

  /**
   * Fungsi untuk login admin.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function adminlogin(Request $request)
  {
    $credentials = $request->only('email', 'password');

    $validator = Validator::make($credentials, [
      'email' => 'required|email|exists:users,email', // Update validation rules to check for existence of email field
      'password' => 'required',
    ]);

    if ($validator->fails()) {
      $errors = $validator->errors();
      if ($errors->has('email')) {
        return redirect()->route('admin-view-login')->with('error', $errors->first('email'));
      }
      if ($errors->has('password')) {
        return redirect()->route('admin-view-login')->with('error', $errors->first('password'));
      }
    }

    $user = User::where('email', $credentials['email'])->first();

    if (!$user) {
      return redirect()->route('admin-view-login')->with('error', 'User not found');
    }

    if ($user->activation !== 'active') {
      return redirect()->route('admin-view-login')->with('error', 'User not active');
    }

    if ($user->role_id != 3) {
      return redirect()->route('admin-view-login')->with('error', 'Invalid role');
    }
    if (!Hash::check($credentials['password'], $user->password)) {
      return redirect()->route('admin-view-login')->with('error', 'Invalid credentials');
    }

    // $token = $user->createToken($credentials['email'])->plainTextToken;
    return redirect()->route('dashboard-analytics')->with('success', 'Welcomeback ' . $user->name);
  }
}
