<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\User;
use Berkayk\OneSignal\OneSignalFacade;

class NotiService extends BaseService
{
    public function useNoti($user_id = null, $title, $status = 'event', $message, $reference_id = null)
    {
        $availability = null;

        if ($user_id) {
            $availability = User::where('id', $user_id)->first();
        }

        $notification = Notification::create([
            'title' => $title,
            'user_id' => $user_id,
            'status' => $status,
            'message' => $message,
            'reference_id' => $reference_id
        ]);

        if ($availability && $availability->available_for_donation !== 'false') {

            if ($user_id) {
                $deviceTokens = DeviceToken::where('user_id', $user_id)
                    ->pluck('device_token')
                    ->toArray();

                if (!empty($deviceTokens)) {
                    $params = [
                        'include_player_ids' => $deviceTokens,
                        'contents' => ['en' => $message],
                        'headings' => ['en' => $title],
                        'data' => [
                            'notification_id' => $notification->id,
                            'status' => $status,
                            'reference_id' => $reference_id
                        ]
                    ];

                    OneSignalFacade::sendNotificationCustom($params);
                }
            } else {
                $params = [
                    'included_segments' => ['All'],
                    'contents' => ['en' => $message],
                    'headings' => ['en' => $title],
                    'data' => [
                        'notification_id' => $notification->id,
                        'status' => $status,
                        'reference_id' => $reference_id
                    ]
                ];

                OneSignalFacade::sendNotificationToAll(
                    $message,
                    $params
                );
            }

            return $notification;
        }

        return null;
    }
}
