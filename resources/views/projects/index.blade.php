@extends('layouts.app')

@section('content')
<h1 class="mb-3">Projects</h1>

@auth
  <a class="btn btn-primary mb-3" href="{{ route('projects.create') }}">New Project</a>
@endauth

<table class="table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Start</th>
      <th>Deadline</th>
      <th>Issues</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @forelse($projects as $p)
      <tr>
        <td>
          <a href="{{ route('projects.show', $p) }}">{{ $p->name }}</a>
          @if($p->is_public)
            <span class="badge text-bg-success ms-1">Public</span>
          @else
            <span class="badge text-bg-secondary ms-1">Private</span>
          @endif
        </td>
        <td>{{ $p->start_date }}</td>
        <td>{{ $p->deadline }}</td>
        <td>{{ $p->issues_count }}</td>
        <td class="text-end">
          @can('update', $p)
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.edit', $p) }}">Edit</a>
          @endcan

          @can('delete', $p)
            <form class="d-inline" method="POST" action="{{ route('projects.destroy', $p) }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</button>
            </form>
          @endcan
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-muted">No projects yet.</td>
      </tr>
    @endforelse
  </tbody>
</table>

{{ $projects->links() }}
@endsection
