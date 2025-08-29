@extends('layouts.app')
@section('content')
<div class="container">
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  <h1 class="mb-1">{{ $project->name }}</h1>
  <p class="text-muted mb-2">{{ $project->start_date }} → {{ $project->deadline }}</p>
  <p>{{ $project->description }}</p>

  <div class="mt-3 mb-2 d-flex gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('projects.edit',$project) }}">Edit</a>
    <a class="btn btn-link" href="{{ route('projects.index') }}">Back</a>
  </div>

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
      <tr><td colspan="4" class="text-muted">No issues yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
