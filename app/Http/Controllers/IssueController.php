<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;

class IssueController extends Controller
{
    public function __construct()
    {
        // Guests can browse index/show; all writes require login
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        // Join projects to enforce visibility; eager load to avoid N+1
        $q = Issue::query()
            ->with(['project', 'tags'])
            ->join('projects', 'projects.id', '=', 'issues.project_id')
            ->select('issues.*');

        // Visibility:
        //  - guests: public only
        //  - authed: public OR owned OR assigned-to-me
        if (auth()->guest()) {
            $q->where('projects.is_public', true);
        } else {
            $uid = auth()->id();
            $q->where(function ($w) use ($uid) {
                $w->where('projects.is_public', true)
                  ->orWhere('projects.user_id', $uid)
                  ->orWhereExists(function ($sub) use ($uid) {
                      $sub->from('issue_user')
                          ->whereColumn('issue_user.issue_id', 'issues.id')
                          ->where('issue_user.user_id', $uid);
                  });
            });
        }

        // Keyword search (AND across terms; OR within title/description)
        if ($kw = trim(request('q', ''))) {
            $terms = preg_split('/\s+/', $kw, -1, PREG_SPLIT_NO_EMPTY);
            $q->where(function ($outer) use ($terms) {
                foreach ($terms as $t) {
                    $like = "%{$t}%";
                    $outer->where(function ($w) use ($like) {
                        $w->where('issues.title', 'like', $like)
                          ->orWhere('issues.description', 'like', $like);
                    });
                }
            });
        }

        // Filters
        if ($s = request('status'))   { $q->where('issues.status', $s); }
        if ($p = request('priority')) { $q->where('issues.priority', $p); }
        if ($tagId = (int) request('tag_id')) {
            $q->whereHas('tags', fn ($t) => $t->where('tags.id', $tagId));
        }

        // Sorting
        switch (request('sort')) {
            case 'due_asc':
                // Nulls last, earliest due first
                $q->orderByRaw('CASE WHEN issues.due_date IS NULL THEN 1 ELSE 0 END, issues.due_date ASC');
                break;

            case 'prio_desc':
                // high > medium > low, then newest
                $q->orderByRaw("CASE issues.priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
                  ->orderByDesc('issues.created_at');
                break;

            default:
                // Default: newest first
                $q->orderByDesc('issues.created_at');
                break;
        }

        $issues = $q->paginate(10)->withQueryString();
        $tags   = Tag::orderBy('name')->get();

        // AJAX: return only the list fragment
        if (request()->ajax()) {
            return view('issues._list', compact('issues'))->render();
        }

        // Full page
        return view('issues.index', compact('issues', 'tags'));
    }

    public function create()
    {
        // Only allow creating issues under projects the user owns
        $projects = Project::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('issues.create', [
            'projects' => $projects,
            'defaults' => ['status' => 'open', 'priority' => 'medium'],
        ]);
    }

    public function store(StoreIssueRequest $request)
    {
        // Ensure the selected project belongs to the user
        $project = Project::findOrFail($request->input('project_id'));
        $this->authorize('update', $project); // owner-only

        $issue = Issue::create($request->validated());

        return redirect()
            ->route('issues.show', $issue)
            ->with('ok', 'Issue created.');
    }

    public function show(Issue $issue)
    {
        // Private project visibility: owner or assignee (handled in policy)
        $this->authorize('view', $issue->project);

        $issue->load(['project', 'tags']);
        $allTags = Tag::orderBy('name')->get();

        return view('issues.show', compact('issue', 'allTags'));
    }

    public function edit(Issue $issue)
    {
        // Only owner of the parent project can edit
        $this->authorize('update', $issue->project);

        return view('issues.edit', [
            'issue'    => $issue,
            'projects' => Project::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue)
    {
        // If moving to a different project, that target must belong to me
        if ($request->filled('project_id')) {
            $target = Project::findOrFail($request->input('project_id'));
            $this->authorize('update', $target);
        } else {
            $this->authorize('update', $issue->project);
        }

        $issue->update($request->validated());

        return redirect()
            ->route('issues.show', $issue)
            ->with('ok', 'Issue updated.');
    }

    public function destroy(Issue $issue)
    {
        // Only owner may delete issues under the project
        $this->authorize('update', $issue->project);

        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('ok', 'Issue deleted.');
    }
}
