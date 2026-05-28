<?php

namespace App\Observers;

use App\Models\Mitra;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class MitraObserver
{
    private function logAction(Mitra $mitra, string $action, array $changes = [])
    {
        $userId = null;
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'Mitra',
            'model_id' => $mitra->id,
            'changes' => empty($changes) ? null : $changes,
        ]);
    }

    public function created(Mitra $mitra): void
    {
        $this->logAction($mitra, 'created', $mitra->getAttributes());
    }

    public function updated(Mitra $mitra): void
    {
        $changes = [];
        foreach ($mitra->getDirty() as $key => $value) {
            if ($key === 'last_login_at') continue;
            
            $changes[$key] = [
                'old' => $mitra->getOriginal($key),
                'new' => $value,
            ];
        }
        
        if (!empty($changes)) {
            $this->logAction($mitra, 'updated', $changes);
        }
    }

    public function deleted(Mitra $mitra): void
    {
        $this->logAction($mitra, 'deleted');
    }
}
