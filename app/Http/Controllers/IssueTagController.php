<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class IssueTagController extends Controller
{
    public function attach(Issue $issue, Tag $tag): JsonResponse
    {
        $this->authorize('update', $issue->project); // owner-only
        $issue->tags()->syncWithoutDetaching([$tag->id]);
        $html = view('issues.partials.tag-chips', ['issue' => $issue->fresh('tags')])->render();
        return response()->json(['ok' => true, 'html' => $html], 200);
    }

    public function detach(Issue $issue, Tag $tag): JsonResponse
    {
        $this->authorize('update', $issue->project);
        $issue->tags()->detach($tag->id);
        $html = view('issues.partials.tag-chips', ['issue' => $issue->fresh('tags')])->render();
        return response()->json(['ok' => true, 'html' => $html], 200);
    }
}
