@extends('layouts.app')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Projects</h1>
    <a class="btn btn-primary" href="{{ route('projects.create') }}">New Project</a>
  </div>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  <table class="table">
    <thead><tr><th>Name</th><th>Start</th><th>Deadline</th><th>Issues</th><th></th></tr></thead>
    <tbody>
      @forelse($projects as $p)
      <tr>
        <td><a href="{{ route('projects.show',$p) }}">{{ $p->name }}</a></td>
        <td>{{ $p->start_date }}</td>
        <td>{{ $p->deadline }}</td>
        <td>{{ $p->issues_count }}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.edit',$p) }}">Edit</a>
          <form class="d-inline" method="POST" action="{{ route('projects.destroy',$p) }}">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" class="text-muted">No projects yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $projects->links() }}
</div>
@endsection
