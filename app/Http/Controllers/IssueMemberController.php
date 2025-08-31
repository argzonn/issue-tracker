<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\Request;

class IssueMemberController extends Controller
{
    public function attach(Request $request, Issue $issue)
    {
        // Only the project owner may change assignees
        $this->authorize('update', $issue->project);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $issue->assignees()->syncWithoutDetaching([$data['user_id']]);

        return response()->json([
            'assignees' => $issue->assignees()->get(['id', 'name']),
        ]);
    }

    public function detach(Issue $issue, User $user)
    {
        $this->authorize('update', $issue->project);

        $issue->assignees()->detach($user->id);

        return response()->json(['ok' => true]);
    }
}
