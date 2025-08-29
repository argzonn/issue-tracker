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
        $query = Issue::with(['project','tags'])
            ->when(request('status'), fn($q,$v) => $q->where('status',$v))
            ->when(request('priority'), fn($q,$v) => $q->where('priority',$v))
            ->when(request('tag_id'), fn(Builder $q,$v) => $q->whereHas('tags', fn($t)=>$t->where('tags.id',$v)))
            ->latest();

        $issues = $query->paginate(10)->withQueryString();
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
        // comments via AJAX; tags loaded for the picker
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
