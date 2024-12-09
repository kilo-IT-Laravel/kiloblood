<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\Banner;
use Exception;
use Illuminate\Support\Facades\Auth;

class bannerManagment extends Koobeni
{

    public function getAllBanners()
    {
        try {

            $banners = $this->bannerService->getAllBanners();

            return $this->paginationDataResponse($banners);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function store()
    {
        try {
            $validated = $this->req->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $banner = $this->bannerService->create($validated);

            $this->logService->log(Auth::id(), 'created_banner', Banner::class, $banner->id, json_encode($validated));

            return $this->dataResponse($banner);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }


    public function update(int $bannerId)
    {
        try {
            $validated = $this->req->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            $banner = Banner::findOrFail($bannerId);
            $updatedBanner = $this->bannerService->update($banner, $validated);

            $this->logService->log(Auth::id(), 'updated_banner', Banner::class, $updatedBanner->id, json_encode($validated));

            return $this->dataResponse( $updatedBanner);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $bannerId)
    {
        try {
            $banner = Banner::findOrFail($bannerId);
            $this->bannerService->delete($banner);

            $this->logService->log(Auth::id(), 'deleted_banner', Banner::class, $bannerId, null);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore(int $bannerId)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($bannerId);
            $this->bannerService->restore($banner);

            $this->logService->log(Auth::id(), 'restored_banner', Banner::class, $bannerId, null);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete(int $bannerId)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($bannerId);
            $this->bannerService->forceDelete($banner);

            $dataToDelete = [
                'id' => $banner->id,
                'image' => $banner->image,
                'title' => $banner->title,
                'description' => $banner->description,
                'link' => $banner->link,
                'order' => $banner->order,
                'is_active' => $banner->is_active,
            ];

            $this->logService->log(Auth::id(), 'force_remove_banner', Banner::class, $bannerId, json_encode([
                'model' => get_class($banner),
                'data' => $dataToDelete
            ]));

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {

            $banners = $this->bannerService->getAllBanners(true);

            return $this->paginationDataResponse($banners);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }


    public function toggleStatus(int $bannerId)
    {
        try {
            $banner = Banner::findOrFail($bannerId);
            $updatedBanner = $this->bannerService->toggleStatus($banner);

            return $this->dataResponse(
                $updatedBanner,
                $updatedBanner->is_active ? 'Banner activated' : 'Banner deactivated'
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

            $this->bannerService->bulkRestore($this->req->ids);

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

            $this->bannerService->bulkForceDelete($this->req->ids);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function reorder()
    {
        try {
            $this->req->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:banners,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            $this->bannerService->reorder($this->req->orders);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $bannerId)
    {
        try {
            $banner = Banner::findOrFail($bannerId);
            return $this->dataResponse($banner);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
