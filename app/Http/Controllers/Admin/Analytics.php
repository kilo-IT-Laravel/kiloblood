<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use Exception;

class analytics extends Koobeni
{

    public function index()
    {
        try {

            $totalRequests = BloodRequest::count();
            $completedRequests = BloodRequest::where('status', 'accepted')->count();
            $totalDonors = BloodRequestDonor::where('status', 'completed')->count();

            $bloodTypeStats = BloodRequest::select('blood_type')
                ->selectRaw('COUNT(*) as total_requests')
                ->selectRaw('SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as completed_requests')
                ->groupBy('blood_type')
                ->get();

            $monthlyStats = BloodRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
                ->selectRaw('COUNT(*) as total_requests')
                ->selectRaw('SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as completed_requests')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            return $this->dataResponse([
                'overview' => [
                    'total_requests' => $totalRequests,
                    'completed_requests' => $completedRequests,
                    'total_donors' => $totalDonors,
                    'completion_rate' => $totalRequests ? ($completedRequests / $totalRequests * 100) : 0
                ],
                'blood_type_stats' => $bloodTypeStats,
                'monthly_stats' => $monthlyStats
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function chart()
    {
        try {

            $monthlyData = BloodRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
                ->selectRaw('COUNT(*) as requests')
                ->selectRaw('SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as completed')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();


            $bloodTypes = BloodRequest::select('blood_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('blood_type')
                ->get();

            return $this->dataResponse([
                'monthly_chart' => $monthlyData,
                'blood_type_chart' => $bloodTypes
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
