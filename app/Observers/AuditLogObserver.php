<?php

namespace App\Observers;

use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogObserver
{
    private function logAction(string $action, Model $model): void
    {
        $userId = Auth::id();
        $tableName = $model->getTable();
        $recordId = $model->id;

        $details = [
            'record_id' => $recordId,
            'new_data' => $model->getChanges(),
            'original_data' => $action === 'UPDATE' ? $model->getOriginal() : null,
        ];


        $description = json_encode($details);

        Log::create([
            'user_id' => $userId,
            'action' => $action,
            'table_affected' => $tableName,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }

    public function created(Model $model): void
    {
        $model->setHidden(['password']);
        $this->logAction('CREATE', $model);
    }

    public function updated(Model $model): void
    {
        $model->setHidden(['password']);
        $this->logAction('UPDATE', $model);
    }

    public function deleted(Model $model): void
    {
        $this->logAction('DELETE', $model);
    }
}
