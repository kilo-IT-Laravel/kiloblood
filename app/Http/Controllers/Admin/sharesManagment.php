<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\Share;
use Exception;
use Illuminate\Support\Facades\Storage;

class sharesManagment extends Koobeni
{
    public function getAllShares()
    {
        try {

            $where = [];

            if ($this->req->language) {
                $where[] = ['language', '=', $this->req->language];
            }

            if ($this->req->is_active) {
                $where[] = ['is_active', '=', $this->req->is_active];
            }

            $data = $this->findAll->allWithPagination([
                'model' => Share::class,
                'sort' => ['order', 'asc'],
                'perPage' => $this->req->perPage,
                'select' => ['id', 'title', 'language', 'is_active', 'order', 'created_at'],
                'search' => [
                    'title' => $this->req->search
                ],
                'where' => $where ?: null,
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function store()
    {
        try {
            $validated = $this->req->validate([
                'title' => 'nullable|string|max:255',
                'message' => 'required|string',
                'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'language' => 'required|in:en,kh,ch',
                'is_active' => 'boolean'
            ]);

            if ($this->req->hasFile('image_url')) {
                $path = $this->req->file('image_url')->store('shares', 'public');
                $validated['image_url'] = $path;
            }

            $share = Share::create($validated);

            return $this->dataResponse($share);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function update(int $shareId)
    {
        try {
            $validated = $this->req->validate([
                'title' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'language' => 'nullable|in:en,kh,ch',
                'is_active' => 'boolean'
            ]);

            $share = Share::findOrFail($shareId);

            if ($this->req->hasFile('image_url')) {
                if ($share->image_url) {
                    Storage::disk('public')->delete($share->image_url);
                }
                $path = $this->req->file('image_url')->store('shares', 'public');
                $validated['image_url'] = $path;
            }

            $share->update($validated);

            return $this->dataResponse($share);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $shareId)
    {
        try {
            $share = Share::findOrFail($shareId);
            $share->delete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore()
    {
        try {
            $share = Share::withTrashed()->findOrFail($this->req->id);
            $share->restore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete()
    {
        try {
            $share = Share::withTrashed()->findOrFail($this->req->id);
            $share->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {
            $where = [];

            if ($this->req->language) {
                $where[] = ['language', '=', $this->req->language];
            }

            if ($this->req->is_active) {
                $where[] = ['is_active', '=', $this->req->is_active];
            }

            $data = $this->findAll->allWithPagination([
                'model' => Share::class,
                'trash' => true,
                'sort' => ['order', 'asc'],
                'perPage' => $this->req->perPage,
                'select' => ['id', 'title', 'language', 'is_active', 'order', 'created_at'],
                'search' => [
                    'title' => $this->req->search
                ],
                'where' => $where ?: null,
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function toggleStatus(int $shareId)
    {
        try {
            $share = Share::findOrFail($shareId);
            $share->update(['is_active' => !$share->is_active]);
            return $this->dataResponse($share, $share->is_active ? 'Share template activated' : 'Share template deactivated',);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:banners,id'
            ]);

            Share::whereIn('id', $this->req->ids)
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
                'ids.*' => 'exists:banners,id'
            ]);

            $banners = Share::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->get();

            foreach ($banners as $banner) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
            }

            Share::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->forceDelete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show()
    {
        try {
            $share = Share::findOrFail($this->req->id);
            return $this->dataResponse($share);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
