<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\Banner;
use App\Services\BannersManagment;
use Exception;

class bannerManagment extends Koobeni
{
    private $bannerService;

    public function __construct()
    {
        $this->bannerService = new BannersManagment();
    }

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

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {

            $banners = $banners = $this->bannerService->getAllBanners(true);

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
