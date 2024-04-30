<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;

class NotificationsController extends Controller
{
    //
    public function index(Request $request)
    {
        $notifications = auth('api')->user()->notifications()->paginate();
        return NotificationResource::collection($notifications);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 未读消息数统计
     */
    public function stats()
    {
        $data = [
            'unread_count' => auth('api')->user()->notification_count,
        ];
        return response()->json($data);
    }

    public function read(Request $request)
    {
        $request->user()->markAsRead();
        return response(null, 204);
    }
}
