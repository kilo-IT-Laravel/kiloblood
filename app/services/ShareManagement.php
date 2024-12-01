<?php

namespace App\Services;

use App\Models\Share;
use App\Models\SocialShare;
use Illuminate\Support\Facades\Storage;

class ShareManagement extends BaseService
{

    public function getAllShares($withTrashed = false)
    {
        $where = [];

        if ($this->req->language) {
            $where[] = ['language', '=', $this->req->language];
        }

        if ($this->req->is_active) {
            $where[] = ['is_active', '=', $this->req->is_active];
        }
        return $this->findAll->allWithPagination([
            'model' => Share::class,
            'trash' => $withTrashed,
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
    }

    public function create(array $data)
    {
        if ($this->req->hasFile('image_url')) {
            $data['image'] = $this->uploadImage($this->req->file('image_url'));
        }

        return Share::create($data);
    }

    public function update(Share $share, array $data)
    {
        if ($this->req->hasFile('image_url')) {
            $this->deleteImage($this->req->file('image_url'));
            $data['image_url'] = $this->uploadImage($this->req->file('image_url'));
        }

        $share->update($data);
        return $share->fresh();
    }

    public function delete(Share $share)
    {
        $share->delete();
        return true;
    }

    public function forceDelete(Share $share)
    {
        $this->deleteImage($share->image);
        $share->forceDelete();
        return true;
    }

    public function restore(Share $share)
    {
        $share->restore();
        return true;
    }

    public function toggleStatus(Share $share)
    {
        $share->update(['is_active' => !$share->is_active]);
        return $share;
    }

    public function bulkRestore()
    {
        return Share::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->restore();
    }

    public function bulkForceDelete()
    {
        $shares = Share::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->get();
        foreach ($shares as $share) {
            $this->deleteImage($share->image);
        }

        return Share::whereIn('id', $this->req->ids)
            ->withTrashed()
            ->forceDelete();
    }

    private function uploadImage($image)
    {
        return $image->store('shares', 'public');
    }

    private function deleteImage($image)
    {
        if ($image) {
            Storage::disk('public')->delete($image);
        }
    }

    public function getAllSocial()
    {
        $where = [];

        if ($this->req->platform) {
            $where[] = ['platform', '=', $this->req->platform];
        }

        if ($this->req->user_id) {
            $where[] = ['user_id', '=', $this->req->user_id];
        }

        return $this->findAll->allWithPagination([
            'model' => SocialShare::class,
            'sort' => 'latest',
            'perPage' => $this->req->perPage,
            'select' => ['id', 'user_id', 'platform', 'created_at'],
            'relations' => [
                'user' => function ($query) {
                    $query->select('id', 'name');
                }
            ],
            'where' => $where ?: null,
            'dateRange' => [
                'startDate' => $this->req->startDate,
                'endDate' => $this->req->endDate
            ]
        ]);
    }

    public function byPlatform()
    {
        return SocialShare::select('platform')
            ->selectRaw('COUNT(*) as total_shares')
            ->groupBy('platform')
            ->get();
    }

    public function topUsers()
    {
        return SocialShare::with('user:id,name')
            ->select('user_id')
            ->selectRaw('COUNT(*) as share_count')
            ->groupBy('user_id')
            ->orderByDesc('share_count')
            ->limit($this->req->limit)
            ->get();
    }

    public function dailyShares()
    {
        return SocialShare::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total_shares')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
