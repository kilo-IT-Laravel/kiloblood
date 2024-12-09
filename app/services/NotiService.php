<?php

namespace App\Services;

use App\Events\notification as EventsNotification;
use App\Models\Notification;

class NotiService extends BaseService {

    public function useNoti($user_id = null , $tatus = 'event' , $message , $reference_id = null) {
        $data = Notification::create([
            'user_id' => $user_id,
            'status' => $tatus,
            'message' => $message,
            'reference_id' => $reference_id
        ]);
        event(new EventsNotification($data));
    }

}