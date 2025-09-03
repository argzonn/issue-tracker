<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IssueCommentStoreRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class IssueCommentController extends Controller
{
    public function store(IssueCommentStoreRequest $request, Issue $issue): JsonResponse
    {
        try {
            // Prefer validated spec names
            $data = $request->validated();

            // Fallback tolerance if your front-end previously used different names
            $data['author_name'] = $data['author_name'] ?? $request->input('author');
            $data['body']        = $data['body'] ?? $request->input('content');

            // Extra guard: if still missing, force a 422 (not 500)
            if (!isset($data['author_name']) || $data['author_name'] === null || $data['author_name'] === '') {
                return response()->json(['ok' => false, 'errors' => ['author_name' => ['Author is required.']]], 422);
            }
            if (!isset($data['body']) || $data['body'] === null || $data['body'] === '') {
                return response()->json(['ok' => false, 'errors' => ['body' => ['Body is required.']]], 422);
            }

            $comment = $issue->comments()->create([
                'author_name' => $data['author_name'],
                'body'        => $data['body'],
            ]);

            $html = view('issues.partials._comment', compact('comment'))->render();

            return response()->json(['ok' => true, 'html' => $html], 201);
        } catch (Throwable $e) {
            Log::error('Issue comment store failed', ['exception' => $e]);
            return response()->json(['ok' => false, 'message' => 'Server error while adding comment.'], 500);
        }
    }
}
