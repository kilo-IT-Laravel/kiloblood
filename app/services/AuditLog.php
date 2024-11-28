<?php

namespace App\Services;

use App\Models\AuditLog as ModelsAuditLog;

class AuditLog
{
    public function log($userId, $action, $modelType, $modelId, $changes = null)
    {
        ModelsAuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $changes,
        ]);
    }
}
