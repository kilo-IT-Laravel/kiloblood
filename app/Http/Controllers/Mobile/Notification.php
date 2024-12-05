<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Notification as ModelsNotification;
use Exception;
use Illuminate\Support\Facades\Auth;

class Notification extends Koobeni
{
    public function index()
    {
        try {
            $data = $this->findAll->allWithLimit([
                'model' => ModelsNotification::class,
                'sort' => 'latest',
                'select' => [
                    'id',
                    'message',
                    'read_at',
                    'created_at'
                ],
                'where' => function ($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhereNull('user_id');
                },
                'limit' => $this->req->perPage,
                'offset' => $this->req->offset
            ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function markAsRead(int $id)
    {
        try {
            $notification = ModelsNotification::findOrFail($id);
            $notification->update(['read_at' => now()]);
            return $this->dataResponse(null, 'Marked as read');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
