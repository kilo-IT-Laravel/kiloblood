<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\Event;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Storage;

class EventManagement extends Koobeni
{
    public function index()
    {
        try {
            $events = $this->eventService->getAllEvents(false);
            return $this->paginationDataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            return $this->dataResponse($event);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function store()
    {
        try {
            $data = $this->req->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $event = $this->eventService->create($data);

            if($event->is_active){
                Notification::create([
                    'status' => 'event',
                    'message' => "New event: {$event->title} at {$event->location}"
                ]);
            }

            return $this->dataResponse($event);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function update(int $eventId)
    {
        try {
            $validated = $this->req->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $event = Event::findOrFail($eventId);
            $updatedEvent = $this->eventService->update($event, $validated);
            return $this->dataResponse($updatedEvent);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $event->delete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {
            $events = $this->eventService->getAllEvents(true);
            return $this->paginationDataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore(int $eventId)
    {
        try {
            $event = Event::withTrashed()->findOrFail($eventId);
            $event->restore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete(int $eventId)
    {
        try {
            $event = Event::withTrashed()->findOrFail($eventId);
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $event->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function toggleStatus(int $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $updatedEvent = $this->eventService->toggleStatus($event);
            return $this->dataResponse(
                $updatedEvent,
                $updatedEvent->is_active ? 'Event activated' : 'Event deactivated'
            );
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function reorder()
    {
        try {
            $this->req->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:events,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            $this->eventService->reorder($this->req->orders);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:events,id'
            ]);

            $this->eventService->bulkRestore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function bulkForceDelete()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:events,id'
            ]);

            $this->eventService->bulkForceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
