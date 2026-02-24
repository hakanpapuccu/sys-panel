<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacation;

class VacationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vacation $vacation): bool
    {
        return $user->id === $vacation->vacation_user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vacation $vacation): bool
    {
        return $user->id === $vacation->vacation_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacation $vacation): bool
    {
        return $user->id === $vacation->vacation_user_id;
    }

    /**
     * Determine whether the user can approve/reject the vacation.
     */
    public function approve(User $user, ?Vacation $vacation = null): bool
    {
        return $user->is_admin;
    }
}
