<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;

class IssueController extends Controller
{
public function index()
{
    // Base builder (eager load to avoid N+1)
    $q = Issue::with(['project','tags']); // ← no ->latest() here

    // Keyword search (AND across terms; OR within title/description)
    if ($kw = trim(request('q', ''))) {
        $terms = preg_split('/\s+/', $kw, -1, PREG_SPLIT_NO_EMPTY);
        $q->where(function ($outer) use ($terms) {
            foreach ($terms as $t) {
                $like = "%{$t}%";
                $outer->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like);
                });
            }
        });
    }

    // Filters
    if ($s = request('status'))   { $q->where('status', $s); }
    if ($p = request('priority')) { $q->where('priority', $p); }
    if ($tagId = (int) request('tag_id')) {
        $q->whereHas('tags', fn($t) => $t->where('tags.id', $tagId));
    }

    // Sorting
    switch (request('sort')) {
        case 'due_asc':
            // Nulls last, earliest due first
            $q->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            break;

        case 'prio_desc':
            // high > medium > low, then newest
            $q->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
              ->orderByDesc('created_at');
            break;

        default:
            // Default: newest first
            $q->orderByDesc('created_at');
            break;
    }

    $issues = $q->paginate(10)->withQueryString();
    $tags   = Tag::orderBy('name')->get();

    return view('issues.index', compact('issues','tags'));
}


    public function create()
    {
        return view('issues.create', [
            'projects' => Project::orderBy('name')->get(),
            'defaults' => ['status' => 'open', 'priority' => 'medium']
        ]);
    }

    public function store(StoreIssueRequest $request)
    {
        $issue = Issue::create($request->validated());
        return redirect()->route('issues.show', $issue)->with('ok','Issue created.');
    }

    public function show(Issue $issue)
    {
        $issue->load(['project','tags']);
        $allTags = Tag::orderBy('name')->get();

        return view('issues.show', compact('issue','allTags'));
    }

    public function edit(Issue $issue)
    {
        return view('issues.edit', [
            'issue' => $issue,
            'projects' => Project::orderBy('name')->get()
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        $issue->update($request->validated());
        return redirect()->route('issues.show', $issue)->with('ok','Issue updated.');
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();
        return redirect()->route('issues.index')->with('ok','Issue deleted.');
    }
}
