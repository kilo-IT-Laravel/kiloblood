<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\Event;
use Exception;

class EventController extends Koobeni
{
    public function index()
    {
        try {
            $events = Event::where('is_active', true)
                ->select('title' , 'description' , 'location', 'image_url' , 'start_date' , 'end_date' , 'is_active' , 'order')
                ->where('end_date', '>=', now())
                ->orderBy('order')
                ->orderBy('start_date')
                ->get();

            return $this->dataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $eventId)
    {
        try {
            $event = Event::findOrFail($eventId)
            ->where('is_active', true)
            ->where('end_date', '>=', now())->first();
            return $this->dataResponse($event);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
