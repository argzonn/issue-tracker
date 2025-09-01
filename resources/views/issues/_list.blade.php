<table class="table align-middle">
  <thead>
  <tr>
    <th style="width:5%">#</th>
    <th style="width:35%">Title</th>
    <th style="width:20%">Project</th>
    <th style="width:10%">Status</th>
    <th style="width:10%">Priority</th>
    <th style="width:10%" class="text-end">Due</th>
    <th style="width:10%" class="text-end">Comments</th>
  </tr>
  </thead>
  <tbody>
  @forelse($issues as $issue)
    <tr>
      <td>{{ $issue->id }}</td>
      <td>
        <a href="{{ route('issues.show', $issue) }}" class="fw-semibold">{{ $issue->title }}</a>
        <div class="small text-muted text-truncate">{{ Str::limit($issue->description, 120) }}</div>
      </td>
      <td>{{ $issue->project?->name ?? '—' }}</td>
      <td>
        <span class="badge bg-{{ ['open'=>'secondary','in_progress'=>'info','closed'=>'success'][$issue->status] ?? 'secondary' }}">
          {{ Str::headline($issue->status) }}
        </span>
      </td>
      <td>
        <span class="badge bg-{{ ['low'=>'secondary','medium'=>'primary','high'=>'warning','urgent'=>'danger'][$issue->priority] ?? 'secondary' }}">
          {{ Str::headline($issue->priority) }}
        </span>
      </td>
      <td class="text-end">{{ optional($issue->due_date)->format('Y-m-d') ?? '—' }}</td>
      <td class="text-end">{{ $issue->comments_count }}</td>
    </tr>
  @empty
    <tr><td colspan="7" class="text-center text-muted py-4">No issues found.</td></tr>
  @endforelse
  </tbody>
</table>
