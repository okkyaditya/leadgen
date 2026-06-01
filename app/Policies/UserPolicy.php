<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $model)
    {
        return $user->hasRole('admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $model)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, User $model)
    {
        return $user->hasRole('admin');
    }

    // Mitra specific authorizations
    public function viewAnyMitra(User $user)
    {
        return true; // Filtered by controller
    }

    public function createMitra(User $user)
    {
        return true; // Allowed for admin, manager, supervisor, support
    }

    public function updateMitra(User $user, User $mitra)
    {
        if ($mitra->role !== 'mitra') return false;

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('supervisor')) {
            $branch = $user->cabang;
            $mitraCabang = $mitra->cabang;
            $uplineCabang = $mitra->upline?->cabang;
            return $mitraCabang === $branch || $uplineCabang === $branch;
        }

        if ($user->hasRole('support')) {
            return $mitra->supervisor_id === $user->id;
        }

        return false;
    }

    public function deleteMitra(User $user, User $mitra)
    {
        return $this->updateMitra($user, $mitra);
    }
}
