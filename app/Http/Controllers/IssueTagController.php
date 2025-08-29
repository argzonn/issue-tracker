<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function attach(Request $request, Issue $issue)
    {
        $data = $request->validate(['tag_id' => ['required','exists:tags,id']]);
        $issue->tags()->syncWithoutDetaching([$data['tag_id']]);

        return response()->json([
            'ok' => true,
            'tags' => $issue->tags()->orderBy('name')->get(['id','name','color']),
        ]);
    }

    public function detach(Issue $issue, Tag $tag)
    {
        $issue->tags()->detach($tag->id);

        return response()->json([
            'ok' => true,
            'tags' => $issue->tags()->orderBy('name')->get(['id','name','color']),
        ]);
    }
}
