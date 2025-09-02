<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\IssueCommentStoreRequest;
use Illuminate\Support\Facades\View;

class IssueCommentController extends Controller
{
    public function index(Request $request, Issue $issue): JsonResponse
    {
        $comments = $issue->comments()->paginate(10);
        $html = View::make('issues.partials.comment-items', ['comments' => $comments])->render();

        return response()->json(['ok'=>true,'html'=>$html,'next'=>$comments->nextPageUrl()]);
    }

    public function store(IssueCommentStoreRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        $html = View::make('issues.partials.comment-items', ['comments' => collect([$comment])])->render();

        return response()->json(['ok'=>true,'html'=>$html]);
    }
}
