<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Storage;

class EventManagement extends Koobeni
{
    public function index()
    {
        try {
            $events = $this->findAll()->allWithPagination([
                'model' => Event::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => [], /// dont know which to select yet
                'search' => [],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);

            return $this->paginationDataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {
            $events = $this->findAll()->allWithPagination([
                'model' => Event::class,
                'sort' => 'latest',
                'trash' => true,
                'perPage' => $this->req->perPage,
                'select' => [], /// dont know which to select yet
                'search' => [],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);

            return $this->paginationDataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function store()
    {
        try {
            $validated = $this->req->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            if ($this->req->hasFile('image')) {
                $path = $this->req->file('image')->store('events', 'public');
                $validated['image'] = $path;
            }

            if (!isset($validated['order'])) {
                $validated['order'] = Event::max('order') + 1;
            }

            $event = Event::create($validated);

            return $this->success('Event created successfully', $event);
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

            if ($this->req->hasFile('image')) {
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }
                $path = $this->req->file('image')->store('events', 'public');
                $validated['image'] = $path;
            }

            $event->update($validated);

            return $this->dataResponse($event);
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

    public function restore($eventId)
    {
        try {
            $event = Event::withTrashed()->findOrFail($eventId);
            $event->restore();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete($eventId)
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

    public function bulkRestore()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:events,id'
            ]);

            Event::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->restore();

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

            $events = Event::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->get();

            foreach ($events as $event) {
                if ($event->image) {
                    Storage::disk('public')->delete($event->image);
                }
            }

            Event::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->forceDelete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function toggleStatus(int $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $event->update([
                'is_active' => !$event->is_active
            ]);

            return $this->dataResponse(
                $event,
                $event->is_active ? 'Event activated' : 'Event deactivated',

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

            foreach ($this->req->orders as $item) {
                Event::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show($eventId)
    {
        try {
            $events = Event::findOrFail($eventId);
            return $this->dataResponse($events);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
