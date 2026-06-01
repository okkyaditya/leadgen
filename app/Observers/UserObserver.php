<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    private function logAction(User $user, string $action, array $changes = [])
    {
        $userId = null;
        if (Auth::check()) {
            $userId = Auth::id();
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'User',
            'model_id' => $user->id,
            'changes' => empty($changes) ? null : $changes,
        ]);
    }

    public function created(User $user): void
    {
        $this->logAction($user, 'created', $user->getAttributes());
    }

    public function updated(User $user): void
    {
        $changes = [];
        foreach ($user->getDirty() as $key => $value) {
            if ($key === 'last_login_at' || $key === 'remember_token') continue;
            
            $changes[$key] = [
                'old' => $user->getOriginal($key),
                'new' => $value,
            ];
        }
        
        if (!empty($changes)) {
            $this->logAction($user, 'updated', $changes);
        }
    }

    public function deleted(User $user): void
    {
        $this->logAction($user, 'deleted');
    }
}
