<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class IssueController extends Controller
{
    public function index()
    {
        $q = \App\Models\Issue::with(['project','tags'])->latest();

    if ($s = request('status'))   { $q->where('status', $s); }
    if ($p = request('priority')) { $q->where('priority', $p); }
    if ($tagId = request('tag_id')) {
        $q->whereHas('tags', fn($t) => $t->where('tags.id', $tagId));
    }

    $issues = $q->paginate(10)->withQueryString();
    $tags   = \App\Models\Tag::orderBy('name')->get();

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

    public function show(\App\Models\Issue $issue)
    {
    $issue->load(['project','tags']);
    $allTags = \App\Models\Tag::orderBy('name')->get();

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
