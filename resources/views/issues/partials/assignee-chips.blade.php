<div id="assignee-chips" class="d-flex flex-wrap gap-2">
  @forelse($issue->assignees as $u)
    <span class="badge rounded-pill text-bg-secondary align-middle" data-user-id="{{ $u->id }}">
      {{ e($u->name) }}
      @can('update', $issue->project)
        <button type="button"
                class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach"
                data-user-id="{{ $u->id }}"
                aria-label="Remove assignee">✕</button>
      @endcan
    </span>
  @empty
    <span class="text-muted">No assignees</span>
  @endforelse
</div>
