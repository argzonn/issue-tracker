<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;

class CommentController extends Controller
{
    /**
     * Require login only for creating comments.
     * Index remains public.
     */
    public function __construct()
    {
        $this->middleware('auth')->only('store'); // ← added
    }

    public function index(Issue $issue)
    {
        $perPage = (int) request('per_page', 5);
        $comments = $issue->comments()->latest()->paginate($perPage);

        return response()->json([
            'data' => $comments->getCollection()->map(fn($c) => [
                'id'               => $c->id,
                'author_name'      => $c->author_name,
                'body'             => $c->body,
                'created_at'       => $c->created_at->toDateTimeString(),
                'created_at_human' => $c->created_at->diffForHumans(),
            ]),
            'next_page_url' => $comments->nextPageUrl(),
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue)
    {
        // Use validated data, but trust the authenticated user for author_name
        $data = $request->validated();

        // If the route is 'auth' only, this is always set; still guard just in case.
        if ($request->user()) {
            $data['author_name'] = $request->user()->name; // ← force from session (prevents spoof)
        }

        $comment = $issue->comments()->create($data);

        return response()->json([
            'ok'      => true,
            'comment' => [
                'id'               => $comment->id,
                'author_name'      => $comment->author_name,
                'body'             => $comment->body,
                'created_at'       => $comment->created_at->toDateTimeString(),
                'created_at_human' => $comment->created_at->diffForHumans(),
            ],
        ], 201);
    }
}
