<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueAssigneeController extends Controller
{
    public function store(Request $request, Issue $issue): JsonResponse
    {
        $data = $request->validate(['user_id' => ['required','integer','exists:users,id']]);
        $issue->assignees()->syncWithoutDetaching([$data['user_id']]);

        return response()->json([
            'ok' => true,
            'assignees' => $issue->assignees()->select('users.id','users.name','users.email')->get(),
        ]);
    }

    public function destroy(Issue $issue, User $user): JsonResponse
    {
        $issue->assignees()->detach($user->id);

        return response()->json([
            'ok' => true,
            'assignees' => $issue->assignees()->select('users.id','users.name','users.email')->get(),
        ]);
    }
}
