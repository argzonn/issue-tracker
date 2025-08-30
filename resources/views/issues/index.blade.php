@extends('layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-3">Issues</h1>

  {{-- Unified filters/search FORM (AJAX reads from this) --}}
  <form id="issue-filters" class="row g-2 mb-3" autocomplete="off">
    {{-- Search --}}
    <div class="col-md-4">
      <input id="search-input" name="q" class="form-control"
             placeholder="Search title or description…"
             value="{{ request('q', '') }}">
    </div>

    {{-- Status --}}
    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">Status</option>
        @foreach(['open','in_progress','closed'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>

    {{-- Priority --}}
    <div class="col-md-2">
      <select name="priority" class="form-select">
        <option value="">Priority</option>
        @foreach(['low','medium','high'] as $p)
          <option value="{{ $p }}" @selected(request('priority')===$p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>

    {{-- Tag --}}
    <div class="col-md-2">
      <select name="tag_id" class="form-select">
        <option value="">Tag</option>
        @foreach($tags as $t)
          <option value="{{ $t->id }}" @selected((string)request('tag_id')===(string)$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    {{-- Sort --}}
    <div class="col-md-2">
      @php $sort = request('sort'); @endphp
      <select name="sort" class="form-select">
        <option value="">Sort: Newest</option>
        <option value="due_asc"   @selected($sort==='due_asc')>Due date ↑</option>
        <option value="prio_desc" @selected($sort==='prio_desc')>Priority: high→low</option>
      </select>
    </div>

    <div class="col-12 d-flex gap-2 mt-1">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('issues.index') }}">Clear</a>
      <a class="btn btn-primary btn-sm ms-auto" href="{{ route('issues.create') }}">New Issue</a>
    </div>

    {{-- Non-AJAX fallback submit (hidden) --}}
    <button type="submit" class="d-none">Apply</button>
  </form>

  {{-- LIST CONTAINER (AJAX renders into here) --}}
  <div id="issue-list">
    @include('issues._list', ['issues' => $issues])
  </div>
</div>

{{-- Inline JS: debounce typing, AJAX load, hijack pagination --}}
<script>
(() => {
  const form = document.getElementById('issue-filters');
  const list = document.getElementById('issue-list');
  const input = document.getElementById('search-input');

  // Debounce helper
  let timer = null;
  const debounce = (fn, ms = 350) => (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(null, args), ms);
  };

  // Serialize form -> URLSearchParams
  function formParams() {
    const fd = new FormData(form);
    return new URLSearchParams(fd);
  }

  // Build URL with current filters
  function buildUrl(base = '{{ route('issues.index') }}') {
    const qs = formParams().toString();
    return qs ? `${base}?${qs}` : base;
  }

  // Fetch list partial
  async function load(url) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
    if (!res.ok) return;
    list.innerHTML = await res.text();
  }

  // 1) Debounced search typing
  input.addEventListener('input', debounce(() => {
    load(buildUrl());
  }, 350));

  // 2) Any select change triggers reload (status/priority/tag/sort)
  form.addEventListener('change', (e) => {
    if (e.target === input) return;
    load(buildUrl());
  });

  // 3) Hijack pagination links inside the list and AJAX them
  list.addEventListener('click', (e) => {
    const a = e.target.closest('.pagination a, a.page-link');
    if (!a) return;
    e.preventDefault();
    const pageUrl = new URL(a.href);
    const params = formParams();
    // Merge page params
    for (const [k, v] of pageUrl.searchParams.entries()) params.set(k, v);
    load(`{{ route('issues.index') }}?${params.toString()}`);
  });
})();
</script>
@endsection
