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
        try{
            $event = Event::findOrFail($eventId);
            return $this->dataResponse($event);
        }catch(Exception $e){
            return $this->handleException($e, $this->req);
        }
    }
}
