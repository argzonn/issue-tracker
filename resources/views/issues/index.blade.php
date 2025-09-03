@extends('layouts.app')
@section('title', 'Issues')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Issues</h1>
    @auth
      <a href="{{ route('issues.create') }}" class="btn btn-primary">New Issue</a>
    @endauth
  </div>

  {{-- Filters / Search (pure GET; selects auto-submit) --}}
  <form class="row g-2 mb-3" method="GET" action="{{ route('issues.index') }}" autocomplete="off">
    {{-- Search --}}
    <div class="col-md-4">
      <input name="q"
             class="form-control"
             placeholder="Search title or description…"
             value="{{ request('q', '') }}">
    </div>

    {{-- Status --}}
    <div class="col-md-2">
      <select name="status" class="form-select" onchange="this.form.submit()">
        <option value="">Status</option>
        @foreach(\App\Models\Issue::STATUSES as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ \Illuminate\Support\Str::headline($s) }}</option>
        @endforeach
      </select>
    </div>

    {{-- Priority --}}
    <div class="col-md-2">
      <select name="priority" class="form-select" onchange="this.form.submit()">
        <option value="">Priority</option>
        @foreach(\App\Models\Issue::PRIORITIES as $p)
          <option value="{{ $p }}" @selected(request('priority')===$p)>{{ \Illuminate\Support\Str::headline($p) }}</option>
        @endforeach
      </select>
    </div>

    {{-- Tag --}}
    <div class="col-md-2">
      <select name="tag_id" class="form-select" onchange="this.form.submit()">
        <option value="">Tag</option>
        @foreach($tags as $t)
          <option value="{{ $t->id }}" @selected((string)request('tag_id')===(string)$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    {{-- Sort --}}
    <div class="col-md-2">
      @php $sort = request('sort'); @endphp
      <select name="sort" class="form-select" onchange="this.form.submit()">
        <option value="">Sort: Newest</option>
        <option value="due_asc"   @selected($sort==='due_asc')>Due date ↑</option>
        <option value="due_desc"  @selected($sort==='due_desc')>Due date ↓</option>
        <option value="prio_asc"  @selected($sort==='prio_asc')>Priority ↑</option>
        <option value="prio_desc" @selected($sort==='prio_desc')>Priority ↓</option>
      </select>
    </div>

    <div class="col-12 d-flex gap-2 mt-1">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('issues.index') }}">Clear</a>
      <button type="submit" class="btn btn-primary btn-sm">Apply</button>
    </div>
  </form>

  @php
    $statusMap   = ['open'=>'secondary','in_progress'=>'info','closed'=>'success'];
    $priorityMap = ['low'=>'secondary','medium'=>'primary','high'=>'warning','urgent'=>'danger'];
  @endphp

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th style="width:70px;">#</th>
          <th>Title</th>
          <th style="width:18%;">Project</th>
          <th style="width:12%;">Status</th>
          <th style="width:12%;">Priority</th>
          <th style="width:10%;">Due</th>
          <th style="width:10%;">Comments</th>
        </tr>
      </thead>
      <tbody>
      @forelse($issues as $issue)
        <tr>
          {{-- Row number (NOT DB id) --}}
          <td>
            @if($issues instanceof \Illuminate\Pagination\LengthAwarePaginator || $issues instanceof \Illuminate\Pagination\Paginator)
              {{ $issues->firstItem() + $loop->index }}
            @else
              {{ $loop->iteration }}
            @endif
          </td>

          <td>
            <a href="{{ route('issues.show', $issue) }}">{{ $issue->title }}</a>
            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($issue->description, 140) }}</div>
          </td>

          <td>{{ $issue->project?->name ?? '—' }}</td>

          <td>
            @php $cls = $statusMap[$issue->status] ?? 'secondary'; @endphp
            <span class="badge bg-{{ $cls }}">{{ \Illuminate\Support\Str::headline($issue->status) }}</span>
          </td>

          <td>
            @php $cls = $priorityMap[$issue->priority] ?? 'secondary'; @endphp
            <span class="badge bg-{{ $cls }}">{{ \Illuminate\Support\Str::headline($issue->priority) }}</span>
          </td>

          <td>{{ optional($issue->due_date)->toDateString() ?? '—' }}</td>
          <td>{{ $issue->comments()->count() }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted">No issues found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Bootstrap pager (AppServiceProvider::boot -> Paginator::useBootstrapFive()) --}}
  {{ $issues->withQueryString()->links() }}
</div>
@endsection
