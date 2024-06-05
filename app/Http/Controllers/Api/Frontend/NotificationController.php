<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NotificationCategory;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        $notification = Notice::with('productCategories', 'notice_service', 'notificationCategory')
            ->get();

        return response()->json([
            'data' => $notification ?? [],
            'success' => true
        ]);
    }

    public function getSingleNotification($id)
    {
        $notification = Notice::with('productCategories', 'notice_service', 'notificationCategory')
            ->find($id);

        if ($notification) {

            return response()->json([
                'data' => $notification,
                'success' => true,
                'message' => ''
            ]);
        } else {

            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Notification not found'
            ]);
        }
    }

    public function getNotificationCategory()
    {

        $notificationCategory = NotificationCategory::with('notices')
            ->get();

        return response()->json([
            'data' => $notificationCategory ?? [],
            'success' => true,
        ], 200);
    }

    public function getSingleNotificationCategory($id)
    {

        $notificationCategory = NotificationCategory::with('notices')
                                                      ->find($id);
        if ($notificationCategory) {

            return response()->json([
                                    'data' => $notificationCategory,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Notification not found'
                                    ], 404);
        }
    }
}
