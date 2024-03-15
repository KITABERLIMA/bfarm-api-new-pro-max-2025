<?php

namespace App\Http\Controllers;

use App\Models\user_admin_notification;
use App\Http\Requests\Updateuser_admin_notificationRequest;
use App\Models\User_company;
use App\Models\user_individual;

class NotificationController extends Controller
{
    /**
     * Get the user's name from user_company or user_individual table based on user_id.
     * Create a sentence with the name and return it.
     */
    public static function getNameById($user_id)
    {
        $user_company = User_company::where('user_id', $user_id)->first();
        $user_individual = user_individual::where('user_id', $user_id)->first();

        if ($user_company) {
            $name = $user_company->name;
        } elseif ($user_individual) {
            $name = $user_individual->name;
        } else {
            $name = 'Unknown';
        }

        return $name;
    }


    public static function userRegisterNotif($user_id)
    {
        $name = self::getNameById($user_id);

        $title = "Welcome, $name!";
        $description = "Thank you for registering. We are excited to have you as part of our community. Explore our platform and discover the amazing features we offer.";

        $notification = [
            'title' => $title,
            'description' => $description
        ];

        return $notification;
    }

    public static function adminRegisterNotif($user_id)
    {
        $name = self::getNameById($user_id);

        $title = "New User, $name";
        $description = "A new user, $name, has registered on our platform.";

        $notification = [
            'title' => $title,
            'description' => $description
        ];

        return $notification;
    }

    /**
     * Display the specified resource.
     */
    public function show(user_admin_notification $user_admin_notification)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user_admin_notification $user_admin_notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateuser_admin_notificationRequest $request, user_admin_notification $user_admin_notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_admin_notification $user_admin_notification)
    {
        //
    }
}