<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IssueCommentController extends Controller
{
    public function index(Request $request, Issue $issue): JsonResponse
    {
        $comments = $issue->comments()->paginate(10);
        $html = View::make('issues.partials.comment-items', ['comments' => $comments])->render();

        return response()->json(['ok'=>true,'html'=>$html,'next'=>$comments->nextPageUrl()]);
    }

    public function store(Request $request, Issue $issue): JsonResponse
    {
        $data = $request->validate([
            'author_name' => ['required','string','max:100'],
            'body' => ['required','string','max:2000'],
        ]);
        $comment = $issue->comments()->create($data);

        $html = View::make('issues.partials.comment-items', ['comments' => collect([$comment])])->render();

        return response()->json(['ok'=>true,'html'=>$html]);
    }
}
