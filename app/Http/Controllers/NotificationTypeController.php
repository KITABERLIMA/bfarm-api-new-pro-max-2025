<?php

namespace App\Http\Controllers;

use App\Models\notificationType;
use App\Http\Requests\StorenotificationTypeRequest;
use App\Http\Requests\UpdatenotificationTypeRequest;

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
  public function store(StorenotificationTypeRequest $request)
  {
    //
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
  public function update(UpdatenotificationTypeRequest $request, notificationType $notificationType)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(notificationType $notificationType)
  {
    //
  }
}
