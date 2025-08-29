@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Issues</h1>

  <form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
      <select name="status" class="form-select" onchange="this.form.submit()">
        <option value="">Status</option>
        @foreach(['open','in_progress','closed'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <select name="priority" class="form-select" onchange="this.form.submit()">
        <option value="">Priority</option>
        @foreach(['low','medium','high'] as $p)
          <option value="{{ $p }}" @selected(request('priority')===$p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <select name="tag_id" class="form-select" onchange="this.form.submit()">
        <option value="">Tag</option>
        @foreach($tags as $t)
          <option value="{{ $t->id }}" @selected((string)request('tag_id')===(string)$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <a class="btn btn-outline-secondary" href="{{ route('issues.index') }}">Clear</a>
    </div>
    <div class="col-auto ms-auto">
      <a class="btn btn-primary" href="{{ route('issues.create') }}">New Issue</a>
    </div>
  </form>

  <table class="table">
    <thead><tr><th>Title</th><th>Project</th><th>Status</th><th>Priority</th><th>Due</th><th></th></tr></thead>
    <tbody>
      @forelse($issues as $i)
      <tr>
        <td><a href="{{ route('issues.show',$i) }}">{{ $i->title }}</a></td>
        <td>{{ $i->project->name }}</td>
        <td>{{ $i->status }}</td>
        <td>{{ $i->priority }}</td>
        <td>{{ $i->due_date }}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('issues.edit',$i) }}">Edit</a>
          <form class="d-inline" method="POST" action="{{ route('issues.destroy',$i) }}">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="text-muted">No issues found.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $issues->links() }}
</div>
@endsection
