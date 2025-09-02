<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueTagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Issue $issue): JsonResponse
    {
        $project = $issue->project()->select('id','owner_id','is_public')->first();
        if (!$project) {
            return response()->json(['ok' => false, 'message' => 'Parent project not found.'], 404);
        }
        $this->authorize('update', $project);

        $data = $request->validate([
            'tag_id' => ['required','integer','exists:tags,id'],
        ]);

        $issue->tags()->syncWithoutDetaching([$data['tag_id']]);

        return response()->json([
            'ok'  => true,
            'tags'=> $issue->tags()
                ->select('tags.id','tags.name','tags.color')
                ->orderBy('tags.name')   // <-- qualify column
                ->get(),
        ], 200);
    }

    public function destroy(Issue $issue, Tag $tag): JsonResponse
    {
        $project = $issue->project()->select('id','owner_id','is_public')->first();
        if (!$project) {
            return response()->json(['ok' => false, 'message' => 'Parent project not found.'], 404);
        }
        $this->authorize('update', $project);

        $issue->tags()->detach($tag->id);

        return response()->json([
            'ok'  => true,
            'tags'=> $issue->tags()
                ->select('tags.id','tags.name','tags.color')
                ->orderBy('tags.name')   // <-- qualify column
                ->get(),
        ], 200);
    }
}
