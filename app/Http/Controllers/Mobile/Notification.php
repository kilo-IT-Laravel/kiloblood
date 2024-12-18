<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Notification as ModelsNotification;
use App\Models\ReadedAt;
use Exception;
use Illuminate\Support\Facades\Auth;

class Notification extends Koobeni
{
    public function index()
    {
        try {
            $availablity = $this->req->user()->available_for_donation;

            if ($availablity && $availablity == 1) {
                $data = $this->findAll->allWithPagination([
                    'model' => ModelsNotification::class,
                    'sort' => 'latest',
                    'select' => [
                        'id',
                        'message',
                        'status',
                        'created_at'
                    ],
                    'where' => function ($query) {
                        $query->where('user_id', Auth::id())
                            ->orWhereNull('user_id');
                    },
                    'whereDoesntHave' => [
                        'readedat' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }
                    ],
                    'perPage' => $this->req->perPage
                ]);
                return $this->paginationDataResponse($data);
            }
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function markAsRead(int $id)
    {
        try {
            ReadedAt::create([
                'read_at' => now(),
                'user_id' => Auth::id(),
                'notification_id' => $id
            ]);
            return $this->dataResponse(null, 'Marked as read');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function viewDetails(int $id)
    {
        try {
            $data = ModelsNotification::findOrFail($id);

            switch ($data->status) {
                case 'confirm':
                    $confirm = $this->donorService->getDonorNotiDetails($data);
                    return $this->dataResponse($confirm);
                    break;
                case 'event':
                    $event = $this->eventService->getEventNotiDetails($data);
                    return $this->dataResponse($event);
                    break;
                default:
                    return 'Invalid notification status';
                    break;
            }
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
