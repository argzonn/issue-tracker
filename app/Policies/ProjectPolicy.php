<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(?User $user): bool
    {
        // Controller limits visibility; guests can still see public items in index
        return true;
    }

    public function view(?User $user, Project $project): bool
    {
        // Public projects are visible to everyone
        if ($project->is_public) {
            return true;
        }

        // Private projects: must be logged in
        if (!$user) {
            return false;
        }

        // Owner can view
        if ((int)$user->id === (int)$project->owner_id) {
            return true;
        }

        // Assignees of any issue in this project can view
        // (assumes a User->assignedIssues() belongsToMany relation)
        return $user->assignedIssues()
            ->where('issues.project_id', $project->id)
            ->exists();
    }

    // Any logged-in user can create
    public function create(User $user): bool
    {
        return true;
    }

    // Only owner can modify
    public function update(User $user, Project $project): bool
{
    return $project->owner_id === $user->id || $user->is_admin ?? false;
}

    public function delete(User $user, Project $project): bool
    {
        return (int)$user->id === (int)$project->owner_id;
    }
}
