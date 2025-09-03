<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Http\Requests\IssueCommentStoreRequest;

class IssueCommentController extends Controller
{
    public function store(IssueCommentStoreRequest $request, Issue $issue)
    {
        // Optional: ensure viewer can access the project/issue
        $this->authorize('view', $issue->project);

        $comment = $issue->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated('body'),
        ])->load('user');

        // Reuse the same partial to render a single item
        $html = view('issues.partials.comment-items', [
            'comments' => collect([$comment]),
        ])->render();

        return response()->json([
            'ok'      => true,
            'html'    => $html,
            'count'   => $issue->comments()->count(),
            'comment' => [
                'id'   => $comment->id,
                'at'   => $comment->created_at->toDateTimeString(),
                'user' => $comment->user->only(['id','name']),
            ],
        ]);
    }
}
