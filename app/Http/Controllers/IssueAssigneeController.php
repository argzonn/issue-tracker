<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class IssueAssigneeController extends Controller
{
    public function attach(Issue $issue, User $user): JsonResponse
    {
        $this->authorize('update', $issue->project);
        $issue->assignees()->syncWithoutDetaching([$user->id]);

        $html = view('issues.partials.assignee-chips', [
            'issue' => $issue->fresh('assignees')
        ])->render();

        return response()->json(['ok' => true, 'html' => $html], 200);
    }

    public function detach(Issue $issue, User $user): JsonResponse
    {
        $this->authorize('update', $issue->project);
        $issue->assignees()->detach($user->id);

        $html = view('issues.partials.assignee-chips', [
            'issue' => $issue->fresh('assignees')
        ])->render();

        return response()->json(['ok' => true, 'html' => $html], 200);
    }
}
