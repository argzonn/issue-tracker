<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\Request;

class IssueMemberController extends Controller
{
    public function __construct()
    {
        // Require login to change assignees
        $this->middleware('auth');
    }

    public function attach(Request $request, Issue $issue)
    {
        // Only project owner can modify assignees
        $this->authorize('update', $issue->project);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        // Idempotent attach
        $issue->assignees()->syncWithoutDetaching([$data['user_id']]);

        // Always return JSON 200 with current assignees
        return response()->json([
            'assignees' => $issue->assignees()->orderBy('name')->get(['id','name']),
        ], 200);
    }

    public function detach(Issue $issue, User $user)
    {
        $this->authorize('update', $issue->project);

        $issue->assignees()->detach($user->id);

        return response()->json([
            'assignees' => $issue->assignees()->orderBy('name')->get(['id','name']),
        ], 200);
    }
}
