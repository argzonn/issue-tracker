<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // controller filters visibility
    }

    public function view(?User $user, Project $project): bool
    {
        if ($project->is_public) return true;
        if (!$user) return false;
        if ($user->id === $project->user_id) return true;

        // NEW: assignees of any issue in this project can view
        return $user->assignedIssues()
            ->where('issues.project_id', $project->id)
            ->exists();
    }

    // any logged-in user can create a project
    public function create(User $user): bool
    {
        return true;
    }

    // only owner can modify the project
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
