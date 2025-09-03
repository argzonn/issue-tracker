<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class IssueTagController extends Controller
{
    public function attach(Issue $issue, Tag $tag): JsonResponse
    {
        $this->authorize('update', $issue->project);

        // avoid duplicate pivot rows
        $issue->tags()->syncWithoutDetaching([$tag->id]);

        // reload relations and render chips
        $issue->load('tags:id,name,color');

        $html = view('issues.partials._tags', compact('issue'))->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }

    public function detach(Issue $issue, Tag $tag): JsonResponse
    {
        $this->authorize('update', $issue->project);

        $issue->tags()->detach($tag->id);
        $issue->load('tags:id,name,color');

        $html = view('issues.partials._tags', compact('issue'))->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }
}
