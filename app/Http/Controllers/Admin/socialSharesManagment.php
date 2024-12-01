<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\SocialShare;
use App\Services\ShareManagement;
use Exception;

class socialSharesManagment extends Koobeni
{

    private $shareService;

    public function __construct() {
        $this->shareService = new ShareManagement();
    }

    public function getAllShares()
    {
        try {
            $data = $this->shareService->getAllSocial();

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getAnalytics()
    {
        try {
            $byPlatform = $this->shareService->byPlatform();

            $topUsers = $this->shareService->topUsers();

            $dailyShares = $this->shareService->dailyShares();

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
