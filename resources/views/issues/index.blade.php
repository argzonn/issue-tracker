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

  <form class="row g-2 mb-3" method="GET">
  <div class="col-md-4">
    <input id="issue-search" name="q" class="form-control" placeholder="Search issues…"
           value="{{ request('q') }}">
  </div>

  <div class="col-md-2">
    <select name="status" class="form-select" onchange="this.form.submit()">
      <option value="">Status</option>
      @foreach(['open','in_progress','closed'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <select name="priority" class="form-select" onchange="this.form.submit()">
      <option value="">Priority</option>
      @foreach(['low','medium','high'] as $p)
        <option value="{{ $p }}" @selected(request('priority')===$p)>{{ $p }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <select name="tag_id" class="form-select" onchange="this.form.submit()">
      <option value="">Tag</option>
      @foreach($tags as $t)
        <option value="{{ $t->id }}" @selected((string)request('tag_id')===(string)$t->id)>
          {{ $t->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <select name="sort" class="form-select" onchange="this.form.submit()">
      @php $sort = request('sort'); @endphp
      <option value="">Sort: Newest</option>
      <option value="due_asc"   @selected($sort==='due_asc')>Due date ↑</option>
      <option value="prio_desc" @selected($sort==='prio_desc')>Priority: high→low</option>
    </select>
  </div>

  <div class="col-12 d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('issues.index') }}">Clear</a>
    <span class="text-muted align-self-center">
      {{ $issues->total() }} result{{ $issues->total()===1?'':'s' }}
    </span>
  </div>
</form>

<script>
(function(){
  // 300ms debounce so typing doesn't spam requests
  const input = document.getElementById('issue-search');
  if (!input) return;
  let t=null;
  input.addEventListener('input', function(){
    clearTimeout(t);
    t = setTimeout(()=> input.form.submit(), 300);
  });
})();
</script>


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
