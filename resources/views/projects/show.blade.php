@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-2">
    <div>
        <h1 class="mb-1">{{ $project->name }}</h1>
        <p class="text-muted mb-0">
            {{ $project->start_date }} → {{ $project->deadline }}
        </p>
        @if($project->owner)
            <p class="text-muted mb-0">Owner: {{ $project->owner->name }}</p>
        @endif
    </div>

    <div class="text-end">
        @can('update', $project)
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.edit', $project) }}">Edit</a>
        @endcan

        @can('delete', $project)
            <form class="d-inline" method="POST" action="{{ route('projects.destroy', $project) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this project?')">
                    Delete
                </button>
            </form>
        @endcan
    </div>
</div>

<p class="mt-3">{{ $project->description }}</p>

<hr>

<h3>Issues</h3>
<table class="table">
  <thead>
    <tr>
      <th>Title</th>
      <th>Status</th>
      <th>Priority</th>
      <th>Due</th>
    </tr>
  </thead>
  <tbody>
    @forelse($project->issues as $i)
      <tr>
        <td><a href="{{ route('issues.show', $i) }}">{{ $i->title }}</a></td>
        <td>{{ $i->status }}</td>
        <td>{{ $i->priority }}</td>
        <td>{{ $i->due_date }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="4" class="text-muted">No issues in this project.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection
