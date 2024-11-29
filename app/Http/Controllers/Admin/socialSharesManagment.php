<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\SocialShare;
use Exception;
use Illuminate\Support\Facades\Storage;

class socialSharesManagment extends Koobeni
{
    public function getAllShares()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => SocialShare::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => ['title'],
                'search' => [
                    'title' => $this->req->search
                ],
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
            $validated = $this->req->validate([]);

            $data = SocialShare::create($validated);

            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function update(int $socialId)
    {
        try {
            $validated = $this->req->validate([]);

            $share = SocialShare::findOrFail($socialId);
            $share->update($validated);
            return $this->dataResponse($share);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy(int $socialId)
    {
        try {
            $data = SocialShare::findOrFail($socialId);
            $data->delete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore()
    {
        try {
            $data = SocialShare::onlyTrashed()->first();
            $data->restore();
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete()
    {
        try {
            $data = SocialShare::withTrashed()->first();
            $data->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => SocialShare::class,
                'sort' => 'latest',
                'trash' => true,
                'perPage' => $this->req->perPage,
                'select' => ['title'],
                'search' => [
                    'title' => $this->req->search
                ],
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

    public function bulkRestore()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:banners,id'
            ]);

            SocialShare::whereIn('id', $this->req->ids)
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
                'ids.*' => 'exists:social_shares,id'
            ]);

            $banners = SocialShare::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->get();

            foreach ($banners as $banner) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
            }

            SocialShare::whereIn('id', $this->req->ids)
                ->withTrashed()
                ->forceDelete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $socialId)
    {
        try {
            $data = SocialShare::findOrFail($socialId);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
