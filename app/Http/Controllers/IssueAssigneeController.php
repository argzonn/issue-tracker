<?php declare(strict_types=1);

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
        $issue->load('assignees:id,name');

        $html = view('issues.partials._assignees', compact('issue'))->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }

    public function detach(Issue $issue, User $user): JsonResponse
    {
        $this->authorize('update', $issue->project);

        $issue->assignees()->detach($user->id);
        $issue->load('assignees:id,name');

        $html = view('issues.partials._assignees', compact('issue'))->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }
}
