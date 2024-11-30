<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\SocialShare;
use Exception;

class socialSharesManagment extends Koobeni
{
    public function getAllShares()
    {
        try {

            $where = [];

            if($this->req->platform){
                $where[] = ['platform', '=', $this->req->platform];
            }

            if($this->req->user_id){
                $where[] = ['user_id', '=', $this->req->user_id];
            }

            $data = $this->findAll->allWithPagination([
                'model' => SocialShare::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => ['id', 'user_id', 'platform', 'created_at'],
                'relations' => [
                    'user' => function($query){
                        $query->select('id' , 'name');
                    }
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

    public function getAnalytics()
    {
        try {
            $byPlatform = SocialShare::select('platform')
                ->selectRaw('COUNT(*) as total_shares')
                ->groupBy('platform')
                ->get();

            $topUsers = SocialShare::with('user:id,name')
                ->select('user_id')
                ->selectRaw('COUNT(*) as share_count')
                ->groupBy('user_id')
                ->orderByDesc('share_count')
                ->limit(10)
                ->get();

            $dailyShares = SocialShare::selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as total_shares')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->dataResponse([
                'by_platform' => $byPlatform,
                'top_users' => $topUsers,
                'daily_shares' => $dailyShares,
                'total_shares' => SocialShare::count(),
                'unique_users' => SocialShare::distinct('user_id')->count()
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function destroy()
    {
        try {
            $this->req->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:social_shares,id'
            ]);

            SocialShare::whereIn('id', $this->req->ids)->delete();

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
