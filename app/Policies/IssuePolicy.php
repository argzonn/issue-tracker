<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    public function viewAny(?User $user): bool
    {
        // Controller already filters visibility; allow listing.
        return true;
    }

    public function view(?User $user, Issue $issue): bool
    {
        // Public project: everyone can view the issue
        if ($issue->project?->is_public) {
            return true;
        }

        // Private project: must be logged in
        if (!$user) {
            return false;
        }

        // Owner of the project can view
        if ((int) $issue->project?->owner_id === (int) $user->id) {
            return true;
        }

        // Assignees of the issue can view
        return $issue->assignees()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        // Allowed to open the create page; controller will also ensure target project ownership.
        return true;
    }

    public function update(User $user, Issue $issue): bool
    {
        // Only the project owner may update issues
        return (int) $issue->project?->owner_id === (int) $user->id;
    }

    public function delete(User $user, Issue $issue): bool
    {
        // Only the project owner may delete issues
        return (int) $issue->project?->owner_id === (int) $user->id;
    }
}
