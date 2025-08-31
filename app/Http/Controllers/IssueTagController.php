<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function attach(Request $request, Issue $issue)
    {
        // Only the project owner may change tags
        $this->authorize('update', $issue->project);

        $data = $request->validate([
            'tag_id' => ['required', 'exists:tags,id'],
        ]);

        $issue->tags()->syncWithoutDetaching([$data['tag_id']]);

        return response()->json([
            'tags' => $issue->tags()->get(['id', 'name', 'color']),
        ]);
    }

    public function detach(Issue $issue, Tag $tag)
    {
        $this->authorize('update', $issue->project);

        $issue->tags()->detach($tag->id);

        return response()->json([
            'tags' => $issue->tags()->get(['id', 'name', 'color']),
        ]);
    }
}
