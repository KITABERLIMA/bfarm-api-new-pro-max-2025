<?php

namespace App\Http\Controllers;

use App\Models\notificationType;
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
    return redirect()->route('auth-login-basic');
  }

  public function adminLogin(Request $request)
  {
    $email = $request->input('email');
    $password = $request->input('password');


    $apiUrl = env('APP_URL') . '/admin/login';

    $client = new \GuzzleHttp\Client();

    try {
      $response = $client->post($apiUrl, [
        'json' => [
          'email' => $email,
          'password' => $password
        ]
      ]);

      if ($response->getStatusCode() == 200) {
        $responseData = json_decode($response->getBody(), true);
        return redirect()->route('auth-login-basic')->with('responseData', $responseData);
      } else {
        // Handle unsuccessful response
        throw new \Exception('Failed to login');
        return redirect()->route('login')->with('error', 'Failed to login');
      }
    } catch (\Exception $exception) {
      return redirect()->route('login')->with('error', $exception->getMessage());
    }
  }
}
