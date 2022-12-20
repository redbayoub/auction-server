<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->user()->notifications;
        return JsonResponse::success(null, $notifications);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $notifications = auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return JsonResponse::success('All unread notifications has been marked as read');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->user()->notifications()->delete();;

        return JsonResponse::success('All notifications has been deleted');
    }
}
