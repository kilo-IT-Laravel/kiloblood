<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannersManagment extends BaseService
{
    public function getAllBanners($withTrashed = false)
    {
        return $this->findAll->allWithPagination([
            'model' => Banner::class,
            'sort' => ['order', 'asc'],
            'trash' => $withTrashed,
            'perPage' => $this->req->perPage,
            'select' => [
                'id',
                'title',
                'order',
                'image',
                'is_active',
                'event_id',
                'created_at'
            ],
            'where' => [
                ['is_active', '=', $this->req->is_active]
            ],
            'search' => [
                'title' => $this->req->search,
                'description' => $this->req->search
            ],
            'dateRange' => [
                'startDate' => $this->req->startDate,
                'endDate' => $this->req->endDate
            ]
        ]);
    }

    public function create(array $data)
    {
        if ($this->req->hasFile('image')) {
            //$data['image'] = $this->uploadImage($this->req->file('image'));
            $path = $data['image']->store('banners', 's3');
            $data['image'] = env('AWS_URL'). $path;
        }

        if (!isset($data['order'])) {
            $data['order'] = $this->getNextOrder();
        }

        return Banner::create($data);
    }


    public function update(Banner $banner, array $data)
    {
        if ($this->req->hasFile('image')) {
            $this->deleteImage($this->req->file('image'));
            $data['image'] = $this->uploadImage($this->req->file('image'));
        }

        $banner->update($data);
        return $banner->fresh();
    }


    public function delete(Banner $banner)
    {
        $banner->delete();
        return true;
    }

    public function forceDelete(Banner $banner)
    {
        $this->deleteImage($banner->image);
        $banner->forceDelete();
        return true;
    }


    public function restore(Banner $banner)
    {
        $banner->restore();
        return $banner;
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return $banner;
    }

    public function bulkRestore()
    {
        return Banner::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->restore();
    }

    public function bulkForceDelete()
    {
        $banners = Banner::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->get();

        foreach ($banners as $banner) {
            if ($banner->image) {
                Storage::disk('s3')->delete($banner->image);
            }
        }

        return Banner::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->forceDelete();
    }

    public function reorder()
    {
        foreach ($this->req->orders as $item) {
            Banner::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }
        return true;
    }

    private function uploadImage($image)
    {
        return $image->store('banners', 's3');
    }

    private function deleteImage($image)
    {
        if ($image) {
            Storage::disk('s3')->delete($image);
        }
    }


    private function getNextOrder()
    {
        return Banner::max('order') + 1;
    }
}
