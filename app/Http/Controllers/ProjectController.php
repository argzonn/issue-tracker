<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Guests can view index/show; auth required for mutating actions
        $this->middleware('auth')->except(['index', 'show']);

        // Maps resource actions to policies (viewAny/view/create/update/delete)
        // Binds {project} parameter to Project model for authorization checks
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * List projects:
     *  - Guests: only public
     *  - Auth users: public OR owned OR where user is assigned to any issue in the project
     */
    public function index()
    {
        $query = Project::query()
            ->withCount('issues')
            ->latest('projects.created_at');

        if (auth()->guest()) {
            $query->where('is_public', true);
        } else {
            $uid = (int) auth()->id();

            $query->where(function (Builder $q) use ($uid) {
                $q->where('is_public', true)
                  ->orWhere('owner_id', $uid)
                  ->orWhereExists(function ($sub) use ($uid) {
                      // Show projects where the current user is assigned to any issue
                      $sub->from('issues')
                          ->join('issue_user', 'issue_user.issue_id', '=', 'issues.id')
                          ->whereColumn('issues.project_id', 'projects.id')
                          ->where('issue_user.user_id', $uid);
                  });
            });
        }

        $projects = $query->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    /** Show create form */
    public function create()
    {
        return view('projects.create');
    }

    /** Persist a new project (owner = current user) */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['owner_id']  = auth()->id();                 // <- important
        $data['is_public'] = $request->boolean('is_public');

        $project = Project::create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('ok', 'Project created.');
    }

    /** Show a single project (policy handles visibility) */
    public function show(Project $project)
    {
        // authorizeResource already invoked 'view' policy
        $project->load(['issues' => fn ($q) => $q->latest()]);
        return view('projects.show', compact('project'));
    }

    /** Show edit form (owner only) */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /** Update a project (owner only) */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $data['is_public'] = $request->boolean('is_public');

        // Never allow owner_id changes via mass update
        unset($data['owner_id']);

        $project->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('ok', 'Project updated.');
    }

    /** Delete a project (owner only) */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('ok', 'Project deleted.');
    }
}
