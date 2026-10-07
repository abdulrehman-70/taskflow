<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Project $project): bool
    {
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $project->members()
                ->where('user_id', $user->id)
                ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }

    public function addMember(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $project->members()
                ->where('user_id', $user->id)
                ->where('role', 'manager')
                ->exists();
    }
    public function removeMember(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $project->members()
                ->where('user_id', $user->id)
                ->where('role', 'manager')
                ->exists();
    }
    public function inviteMember(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $project->members()
                ->where('user_id', $user->id)
                ->where('role', 'manager')
                ->exists();
    }
}
