<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Login required for create/edit/update/destroy; guests can still hit index/show
        $this->middleware('auth')->except(['index', 'show']);

        // Enforce policy on all resource actions (including show)
        $this->authorizeResource(Project::class, 'project');
    }

    public function index()
{
    $query = \App\Models\Project::query()->withCount('issues')->latest();

    if (auth()->guest()) {
        $query->where('is_public', true);
    } else {
        $uid = auth()->id();
        $query->where(function ($q) use ($uid) {
            $q->where('is_public', true)
              ->orWhere('user_id', $uid)
              // NEW: show projects where I'm assigned to any issue
              ->orWhereExists(function ($sub) use ($uid) {
                  $sub->from('issues')
                      ->join('issue_user', 'issue_user.issue_id', '=', 'issues.id')
                      ->whereColumn('issues.project_id', 'projects.id')
                      ->where('issue_user.user_id', $uid);
              });
        });
    }

    $projects = $query->paginate(10);

    return view('projects.index', compact('projects'));
}

    public function create()
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['user_id']   = auth()->id();
        $data['is_public'] = $request->boolean('is_public');

        $project = Project::create($data);

        return redirect()->route('projects.show', $project)->with('ok', 'Project created.');
    }

    public function show(Project $project)
    {
        // authorizeResource already calls policy 'view' for show()
        $project->load(['issues' => fn ($q) => $q->latest()]);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['is_public'] = $request->boolean('is_public');

        $project->update($data);

        return redirect()->route('projects.show', $project)->with('ok', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('ok', 'Project deleted.');
    }
}
