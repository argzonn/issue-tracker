<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;

class CommentController extends Controller
{
    public function index(Issue $issue)
    {
        $perPage = (int) request('per_page', 5);
        $comments = $issue->comments()->latest()->paginate($perPage);

        return response()->json([
            'data' => $comments->getCollection()->map(fn($c)=>[
                'id'=>$c->id,
                'author_name'=>$c->author_name,
                'body'=>$c->body,
                'created_at'=>$c->created_at->toDateTimeString(),
                'created_at_human'=>$c->created_at->diffForHumans(),
            ]),
            'next_page_url' => $comments->nextPageUrl(),
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue)
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'ok'=>true,
            'comment'=>[
                'id'=>$comment->id,
                'author_name'=>$comment->author_name,
                'body'=>$comment->body,
                'created_at'=>$comment->created_at->toDateTimeString(),
                'created_at_human'=>$comment->created_at->diffForHumans(),
            ]
        ], 201);
    }
}
