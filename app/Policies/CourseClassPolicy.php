<?php

namespace App\Policies;

use App\Models\CourseClass;
use App\Models\User;

class CourseClassPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseClass $courseClass): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseClass $courseClass): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseClass $courseClass): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseClass $courseClass): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseClass $courseClass): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can force delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return in_array($user->role->name, ['Owner', 'Admin']);
    }
}
