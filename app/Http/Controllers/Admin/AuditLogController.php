<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\AuditLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogController extends Koobeni
{
    public function getAuditLogs(int $userId)
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => AuditLog::class,
                'sort' => 'latest',
                'where' => [['user_id', '=', $userId]],
                'perPage' => $this->req->perPage,
            ]);
            return $this->paginationDataResponse($this->formattedAuditLog($data));
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function rollbackDelete(int $logId)
    {
        try {

            $logEntry = AuditLog::find($logId);
            if (!$logEntry) {
                throw new Exception('Log entry not found');
            }

            $changes = $this->decodeAndFormatChanges($logEntry->changes);

            if (!is_array($changes) || !isset($changes['model']) || !isset($changes['data'])) {
                Log::error('Invalid log data structure for ID: ' . $logId);
                throw new Exception('Invalid log data');
            }

            $modelClass = $changes['model'];
            $data = $changes['data'];

            if (!class_exists($modelClass)) {
                Log::error("Model class not found: {$modelClass}");
                throw new Exception("Model class {$modelClass} not found");
            }

            $modelClass::create($data);
            Log::info("Successfully rolled back delete for {$modelClass} with ID: {$data['id']}");
            return $this->dataResponse(null, "Successfully rolled back delete for {$modelClass} with ID: {$data['id']}");
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
