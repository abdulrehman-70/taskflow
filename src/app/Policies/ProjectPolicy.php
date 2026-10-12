<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view projects.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_project');
    }

    /**
     * Determine whether the user can view a project.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->can('view_project')
            && (
                $project->created_by === $user->id
                || $project->members()
                    ->where('user_id', $user->id)
                    ->exists()
            );
    }

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->can('create_project');
    }

    /**
     * Determine whether the user can update a project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->can('update_project')
            && $project->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete a project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->can('delete_project')
            && $project->created_by === $user->id;
    }

    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }

    public function addMember(User $user, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    public function removeMember(User $user, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    public function inviteMember(User $user, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    private function canManageMembers(User $user, Project $project): bool
    {
        return $user->can('invite_member')
            && (
                $project->created_by === $user->id
                || $project->members()
                    ->where('user_id', $user->id)
                    ->where('role', 'manager')
                    ->exists()
            );
    }
}
