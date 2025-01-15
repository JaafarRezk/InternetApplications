<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class NotificationController extends Controller
{
    
    public function sendNotification(Request $request)
{
    public function sendNotification(Request $request)
    {
        // التحقق من البيانات المستلمة
        $request->validate([
            'message' => 'required|string|max:255', // رسالة الإشعار
            'user_id' => 'required|integer|exists:users,id', // معرّف المستخدم المستهدف
        ]);

        // الحصول على البيانات
        $message = $request->input('message');
        $userId = $request->input('user_id');

        // بث الإشعار
        event(new NotificationEven($message, $userId));

        // الاستجابة
        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully!',
        ]);
    }  
}



}
