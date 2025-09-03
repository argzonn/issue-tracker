<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use App\Http\Requests\IssueCommentStoreRequest;

class IssueCommentController extends Controller
{
    public function index(Request $request, Issue $issue)
    {
        $comments = $issue->comments()->latest()->paginate(10);

        $html = view('issues.partials.comment-items', [
            'comments' => $comments->items(),
        ])->render();

        return response()->json([
            'html' => $html,
            'next' => $comments->hasMorePages() ? $comments->nextPageUrl() : null,
        ]);
    }

    public function store(IssueCommentStoreRequest $request, Issue $issue)
    {
        $comment = $issue->comments()->create($request->validated());

        $html = view('issues.partials.comment-items', [
            'comments' => [$comment],
        ])->render();

        return response()->json([
            'ok'    => true,
            'html'  => $html,
            'count' => $issue->comments()->count(),
        ], 201);
    }
}
