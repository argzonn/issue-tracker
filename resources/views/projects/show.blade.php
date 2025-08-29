@extends('layouts.app')

@section('content')
<h1 class="mb-1">{{ $project->name }}</h1>
<p class="text-muted">{{ $project->start_date }} → {{ $project->deadline }}</p>
<p>{{ $project->description }}</p>

<hr>
<h3>Issues</h3>
<table class="table">
  <thead><tr><th>Title</th><th>Status</th><th>Priority</th><th>Due</th></tr></thead>
  <tbody>
    @forelse($project->issues as $i)
      <tr>
        <td><a href="{{ route('issues.show',$i) }}">{{ $i->title }}</a></td>
        <td>{{ $i->status }}</td>
        <td>{{ $i->priority }}</td>
        <td>{{ $i->due_date }}</td>
      </tr>
    @empty
      <tr><td colspan="4" class="text-muted">No issues in this project.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
