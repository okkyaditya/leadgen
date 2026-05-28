<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LeadObserver
{
    private function logAction(Lead $lead, string $action, array $changes = [])
    {
        $userId = null;
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'Lead',
            'model_id' => $lead->id,
            'changes' => empty($changes) ? null : $changes,
        ]);
    }

    public function creating(Lead $lead): void
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $lead->cabang = $user->cabang ?? null;
        } elseif (Auth::guard('mitra')->check()) {
            $mitra = Auth::guard('mitra')->user();
            $lead->cabang = $mitra->upline?->cabang ?? null;
        }
    }

    public function created(Lead $lead): void
    {
        $this->logAction($lead, 'created', $lead->getAttributes());
    }

    public function updated(Lead $lead): void
    {
        $changes = [];
        foreach ($lead->getDirty() as $key => $value) {
            $changes[$key] = [
                'old' => $lead->getOriginal($key),
                'new' => $value,
            ];
        }
        $this->logAction($lead, 'updated', $changes);
    }

    public function deleted(Lead $lead): void
    {
        $this->logAction($lead, 'deleted');
    }
}
