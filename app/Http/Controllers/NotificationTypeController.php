<?php

namespace App\Http\Controllers;

use App\Models\notificationType;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{
  /**
   * Display the manage types page.
   */
  public function manageTypes()
  {
    $notificationTypes = notificationType::all();
    return view('content.notification.manageType', compact('notificationTypes'));
  }

  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'string|required',
      'description' => 'string|nullable',
    ]);

    $notificationType = new notificationType;
    $notificationType->name = $validatedData['name'];
    $notificationType->description = $validatedData['description'];
    $notificationType->save();

    return redirect()->route('manage-types')->with('success', 'Notification type created successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(notificationType $notificationType)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(notificationType $notificationType)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, notificationType $notificationType)
  {
    $validatedData = $request->validate([
      'name' => 'string|required',
      'description' => 'string|nullable',
    ]);

    $notificationType->name = $validatedData['name'];
    $notificationType->description = $validatedData['description'];
    $notificationType->save();

    return redirect()->route('manage-types')->with('success', 'Notification type updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(notificationType $notificationType)
  {
    $notificationType->delete();

    return redirect()->route('manage-types')->with('success', 'Notification type deleted successfully.');
  }
}
