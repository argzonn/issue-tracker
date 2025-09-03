<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use App\Http\Requests\IssueCommentStoreRequest;

class IssueCommentController extends Controller
{
    /**
     * GET /issues/{issue}/comments  -> JSON { ok, html, next }
     */
    public function index(Request $request, Issue $issue): JsonResponse
    {
        $comments = $issue->comments()
            ->latest()              // newest first
            ->paginate(10);

        $html = View::make('issues.partials.comment-items', ['comments' => $comments])->render();

        return response()->json([
            'ok'   => true,
            'html' => $html,
            'next' => $comments->nextPageUrl(),
        ], Response::HTTP_OK);
    }

    /**
     * POST /issues/{issue}/comments (AJAX)
     * Uses IssueCommentStoreRequest (validates author_name + body)
     * Returns HTML for exactly ONE new comment so you can prepend it.
     */
    public function store(IssueCommentStoreRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        // Reuse your existing "comment-items" partial with a single-item collection
        $html = View::make('issues.partials.comment-items', [
            'comments' => collect([$comment]),
        ])->render();

        // IMPORTANT: 201 so your JS treats it as success
        return response()->json([
            'ok'   => true,
            'html' => $html,
        ], Response::HTTP_CREATED);
    }

    /**
     * DELETE /comments/{comment}
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['ok' => true], Response::HTTP_OK);
    }
}
