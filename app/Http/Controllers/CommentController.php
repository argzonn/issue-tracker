<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IssueCommentStoreRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommentController extends Controller
{
    public function store(IssueCommentStoreRequest $request, Issue $issue): JsonResponse
    {
        try {
            // Support exact spec fields; also tolerate legacy 'author'/'content' names.
            $data = $request->validated();
            $data['author_name'] = $data['author_name'] ?? $request->input('author');
            $data['body']        = $data['body'] ?? $request->input('content');

            $comment = $issue->comments()->create([
                'author_name' => $data['author_name'],
                'body'        => $data['body'],
            ]);

            $html = view('issues.partials._comment', compact('comment'))->render();

            return response()->json(['ok' => true, 'html' => $html], 201);
        } catch (Throwable $e) {
            Log::error('Comment store failed', ['exception' => $e]);
            return response()->json(['ok' => false, 'message' => 'Server error while adding comment.'], 500);
        }
    }
}
