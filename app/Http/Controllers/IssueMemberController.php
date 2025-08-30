<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IssueMemberController extends Controller
{
    // POST /issues/{issue}/assignees
    public function attach(Request $request, Issue $issue)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users','id')],
        ]);

        $issue->assignees()->syncWithoutDetaching([$data['user_id']]);

        $issue->load('assignees:id,name,email');

        return response()->json([
            'ok' => true,
            'assignees' => $issue->assignees,
        ]);
    }

    // DELETE /issues/{issue}/assignees/{user}
    public function detach(Issue $issue, User $user)
    {
        $issue->assignees()->detach($user->id);
        return response()->json(['ok' => true, 'user_id' => $user->id]);
    }
}
