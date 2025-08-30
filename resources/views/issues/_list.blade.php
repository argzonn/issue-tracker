{{-- issues/_list.blade.php --}}
<table class="table align-middle">
  <thead>
    <tr>
      <th style="width:5%">#</th>
      <th style="width:30%">Title</th>
      <th style="width:20%">Project</th>
      <th style="width:10%">Status</th>
      <th style="width:10%">Priority</th>
      <th style="width:10%" class="text-end">Due</th>
      <th style="width:15%" class="text-end">Actions</th>
    </tr>
  </thead>
  <tbody>
    @forelse($issues as $issue)
      <tr>
        <td>{{ $issue->id }}</td>
        <td>
          <a href="{{ route('issues.show', $issue) }}">
            {{ $issue->title }}
          </a>
        </td>
        <td>{{ $issue->project->name ?? '—' }}</td>
        <td>@statusBadge($issue->status)</td>
        <td>@priorityBadge($issue->priority)</td>
        <td class="text-end">{{ $issue->due_date ?? '—' }}</td>
        <td class="text-end">
          <a href="{{ route('issues.edit', $issue) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
          <form action="{{ route('issues.destroy', $issue) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Delete this issue? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center text-muted">No issues found.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="d-flex justify-content-between align-items-center">
  <div class="text-muted small">
    Showing {{ $issues->firstItem() ?? 0 }}–{{ $issues->lastItem() ?? 0 }}
    of {{ $issues->total() }} issue{{ $issues->total() === 1 ? '' : 's' }}
  </div>
  <div>
    {{ $issues->withQueryString()->links() }}
  </div>
</div>
