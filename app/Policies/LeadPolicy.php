<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Everyone can view the index (filtered by controller query)
    }

    public function view(User $user, Lead $lead)
    {
        return true; // View restrictions are handled by the index query
    }

    public function create(User $user)
    {
        return true; // Any authenticated user can create a lead
    }

    public function update(User $user, Lead $lead)
    {
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return true;
        }

        if ($user->hasAnyRole(['supervisor', 'support'])) {
            $isOwnInput = ($lead->input_by === $user->id);
            $isOwnMitraInput = $lead->sourceMitra && ($lead->sourceMitra->supervisor_id === $user->id);
            return $isOwnInput || $isOwnMitraInput;
        }

        if ($user->hasRole('mitra')) {
            return $lead->source_mitra_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Lead $lead)
    {
        return $this->update($user, $lead); // Delete rules are the same as update in this context
    }
}
