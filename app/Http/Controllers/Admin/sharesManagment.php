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
            $data = $this->findAll->allWithPagination([
                'model' => Share::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => [],
                'search' => [],
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
                'title' => 'required|string',
                'message' => 'required|string',
                'image_url' => 'required|string',
                'language' => 'required|string',
                'is_active' => 'boolean',
            ]);

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
                'id' => 'required|exists:shares,id',
                'title' => 'required|string',
                'message' => 'required|string',
                'image_url' => 'required|string',
                'language' => 'required|string',
                'is_active' => 'boolean',
            ]);

            $share = Share::findOrFail($shareId);

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
            $data = $this->findAll->allWithPagination([
                'model' => Share::class,
                'sort' => 'latest',
                'trash' => true,
                'perPage' => $this->req->perPage,
                'select' => [],
                'search' => [],
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
            $share->is_active = !$share->is_active;
            $share->save();
            return $this->dataResponse($share, $share->is_active ? "Share Activated" : "Share Deactivated");
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
