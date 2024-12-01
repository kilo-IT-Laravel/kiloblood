<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\Share;
use App\Services\ShareManagement;
use Exception;
use Illuminate\Support\Facades\Storage;

class sharesManagment extends Koobeni
{
    private $shareService;

    public function __construct()
    {
        $this->shareService = new ShareManagement();
    }

    public function getAllShares()
    {
        try {

            $data = $this->shareService->getAllShares();

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

            $share = $this->shareService->create($validated);

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
            $updateShare = $this->shareService->update($share, $validated);

            return $this->dataResponse($updateShare);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $shareId)
    {
        try {
            $share = Share::findOrFail($shareId);
            $this->shareService->delete($share);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore()
    {
        try {
            $share = Share::withTrashed()->findOrFail($this->req->id);
            $this->shareService->restore($share);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete()
    {
        try {
            $share = Share::withTrashed()->findOrFail($this->req->id);
            $this->shareService->forceDelete($share);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {

            $data = $this->shareService->getAllShares(true);

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function toggleStatus(int $shareId)
    {
        try {
            $share = Share::findOrFail($shareId);
            $updateShare = $this->shareService->toggleStatus($share);
            return $this->dataResponse(
                $updateShare,
                $updateShare->is_active ? 'Share template activated' : 'Share template deactivated',
            );
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

            $this->shareService->bulkRestore($this->req->ids);

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

            $this->shareService->bulkForceDelete($this->req->ids);

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
