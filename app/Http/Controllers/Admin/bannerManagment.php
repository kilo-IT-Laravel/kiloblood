<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\Banner;
use Exception;
use Illuminate\Support\Facades\Storage;

class bannerManagment extends Koobeni
{
    public function getAllBanners()
    {
        try {
            $banners = $this->findAll->allWithPagination([
                'model' => Banner::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => [], /// dont know which to select yet
                'search' => [], /// there gonna be a sort too if there is_active or not
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
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

            if ($this->req->hasFile('image')) {
                $path = $this->req->file('image')->store('banners', 'public');
                $validated['image'] = $path;
            }

            if (!isset($validated['order'])) {
                $validated['order'] = Banner::max('order') + 1;
            }

            $banner = Banner::create($validated);

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

            if ($this->req->hasFile('image')) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }

                $path = $this->req->file('image')->store('banners', 'public');
                $validated['image'] = $path;
            }

            $banner->update($validated);

            return $this->dataResponse($banner);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $bannerId)
    {
        try {
            $banner = Banner::findOrFail($bannerId);

            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->delete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);
            $banner->restore();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);

            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->forceDelete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {
            $banners = $this->findAll->allWithPagination([
                'model' => Banner::class,
                'trash' => true,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'search' => [], /// there gonna be a sort too if there is_active or not
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($banners);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }


    public function toggleStatus(int $bannerId)
    {
        try {
            $banner = Banner::findOrFail($bannerId);
            $banner->update([
                'is_active' => !$banner->is_active
            ]);

            return $this->dataResponse(
                $banner,
                $banner->is_active ? 'Banner activated' : 'Banner deactivated'
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

            Banner::whereIn('id', $this->req->ids)
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

            $banners = Banner::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->get();

            foreach ($banners as $banner) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
            }

            Banner::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->forceDelete();

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

            foreach ($this->req->orders as $item) {
                Banner::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return $this->success('Banners reordered successfully');
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
