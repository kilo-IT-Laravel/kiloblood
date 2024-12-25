<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Storage\utils\CustomResponse;

class EventManagement extends BaseService
{
    use CustomResponse;
    public function getAllEvents($withTrashed = false)
    {
        $where = [];

        if ($this->req->is_active) {
            $where[] = ['is_active', '=', $this->req->is_active];
        }

        if ($this->req->upcoming) {
            $where[] = ['end_date', '>=', $this->req->upcoming];
        }

        $where[] = ['end_date', '>=', now()->toDateString()];

        return $this->findAll->allWithPagination([
            'model' => Event::class,
            'trash' => $withTrashed,
            'sort' => ['start_date', 'asc'],
            'perPage' => $this->req->perPage,
            'select' => [
                'id',
                'title',
                'description',
                'location',
                'image_url',
                'start_date',
                'end_date',
                'is_active',
                'order',
                'created_at'
            ],
            'where' => $where ?: null,
            'search' => [
                'title' => $this->req->search,
                'description' => $this->req->search,
                'location' => $this->req->search
            ],
            'dateRange' => [
                'startDate' => $this->req->startDate,
                'endDate' => $this->req->endDate
            ]
        ]);


    }

    public function create(array $data)
    {
        if ($this->req->hasFile('image_url')) {
            $data['image_url'] = $this->uploadImage($this->req->file('image_url'));
        }

        if (!isset($data['order'])) {
            $data['order'] = $this->getNextOrder();
        }

        return Event::create($data);
    }

    public function update(Event $event, array $data)
    {
        if ($this->req->hasFile('image_url')) {
            $this->deleteImage($this->req->file('image_url'));
            $data['image_url'] = $this->uploadImage($this->req->file('image_url'));
        }

        $event->update($data);
        return $event->fresh();
    }

    public function delete(Event $event)
    {
        $event->delete();
        return true;
    }

    public function forceDelete(Event $event)
    {
        $this->deleteImage($event->image);
        $event->forceDelete();
        return true;
    }

    public function restore(Event $event)
    {
        $event->restore();
        return $event;
    }

    public function toggleStatus(Event $event)
    {
        $event->update(['is_active' => !$event->is_active]);
        return $event;
    }

    public function bulkRestore()
    {
        return Event::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->restore();
    }

    public function bulkForceDelete()
    {
        $events = Event::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->get();

        foreach ($events as $event) {
            $this->deleteImage($event->image);
        }

        return Event::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->forceDelete();
    }

    public function reorder(array $orders)
    {
        foreach ($orders as $item) {
            Event::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }
        return true;
    }

    public function getActiveEvents()
    {
        return Event::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('order')
            ->orderBy('start_date')
            ->get();
    }

    public function getEventNotiDetails($data)
    {
        $eventDetails = Event::where('is_active', true)
            ->select(
                'id',
                'title',
                'description',
                'location',
                'start_date',
                'end_date',
                'is_active'
            )
            ->latest()
            ->first();

        return [
            'notification' => [
                'id' => $data->id,
                'message' => $data->message,
                'time' => $data->created_at->diffForHumans(),
                'status' => $data->status
            ],
            'event_details' => [
                'title' => $eventDetails->title,
                'description' => $eventDetails->description,
                'location' => $eventDetails->location,
                'date' => [
                    'start' => Carbon::parse($eventDetails->start_date)->format('Y-m-d H:i'),
                    'end' => Carbon::parse($eventDetails->end_date)->format('Y-m-d H:i'),
                    'is_active' => $eventDetails->is_active,
                    'is_expired' => Carbon::parse($eventDetails->end_date)->isPast()
                ]
            ]
        ];
    }

    private function uploadImage($image)
    {
        return $image->store('events', 's3');
    }

    private function deleteImage($image)
    {
        if ($image) {
            Storage::disk('s3')->delete($image);
        }
    }

    private function getNextOrder()
    {
        return Event::max('order') + 1;
    }
}
