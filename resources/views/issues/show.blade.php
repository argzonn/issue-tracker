@extends('layouts.app')

@section('content')
@php
    // fallback badge rendering if you don't have @statusBadge / @priorityBadge directives
    $statusBadge = fn(string $s) => [
        'open' => 'secondary',
        'in_progress' => 'info',
        'closed' => 'success',
    ][$s] ?? 'secondary';

    $priorityBadge = fn(string $p) => [
        'low' => 'secondary',
        'medium' => 'primary',
        'high' => 'warning',
        'urgent' => 'danger',
    ][$p] ?? 'secondary';
@endphp

<h1 class="mb-1">{{ $issue->title }}</h1>

<p class="text-muted mb-3">
  Project:
  <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a>
  ·
  <span>Status:
    <span class="badge bg-{{ $statusBadge($issue->status) }}">{{ \Illuminate\Support\Str::headline($issue->status) }}</span>
  </span>
  ·
  <span>Priority:
    <span class="badge bg-{{ $priorityBadge($issue->priority) }}">{{ \Illuminate\Support\Str::headline($issue->priority) }}</span>
  </span>
  ·
  Due:
  <strong>{{ optional($issue->due_date)->format('Y-m-d') ?? '—' }}</strong>
</p>

@if($issue->description)
  <div class="mb-4">{!! nl2br(e($issue->description)) !!}</div>
@endif

<hr class="my-4">

{{-- TAGS (chips) --}}
<h4 class="mb-2">Tags</h4>
<div id="tag-chips" class="mb-3">
  @foreach($issue->tags as $t)
    <span class="badge rounded-pill text-bg-secondary me-2 align-middle" data-tag-id="{{ $t->id }}">
      @if($t->color)
        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $t->color }};margin-right:6px;"></span>
      @endif
      {{ $t->name }}
      @can('update', $issue->project)
        <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="{{ $t->id }}">✕</button>
      @endcan
    </span>
  @endforeach
</div>

@can('update', $issue->project)
  <div class="row g-2 align-items-center mb-4">
    <div class="col-auto">
      <select id="tag-select" class="form-select">
        <option value="">Add a tag…</option>
        @foreach($allTags as $t)
          <option value="{{ $t->id }}">{{ $t->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <button id="tag-attach-btn" class="btn btn-outline-primary">Attach</button>
    </div>
  </div>
@endcan

<hr class="my-4">

{{-- ASSIGNEES (chips) --}}
<h4 class="mb-2">Assignees</h4>
<div id="assignee-chips" class="mb-3">
  @foreach($issue->assignees as $u)
    <span class="badge rounded-pill text-bg-secondary me-2 align-middle" data-user-id="{{ $u->id }}">
      {{ $u->name }}
      @can('update', $issue->project)
        <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="{{ $u->id }}">✕</button>
      @endcan
    </span>
  @endforeach
</div>

@can('update', $issue->project)
  <div class="row g-2 align-items-center mb-4">
    <div class="col-auto">
      <select id="assignee-select" class="form-select">
        <option value="">Assign a user…</option>
        @foreach(\App\Models\User::query()->orderBy('name')->get(['id','name']) as $userOption)
          <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <button id="assignee-attach-btn" class="btn btn-outline-primary">Assign</button>
    </div>
  </div>
@endcan

<hr class="my-4">

{{-- COMMENTS --}}
<h4 class="mb-2">Comments</h4>

@auth
  <div class="card mb-3">
    <div class="card-body">
      <form id="comment-form" class="row g-2">
        @csrf
        <div class="col-md-3">
          <input name="author_name" class="form-control" placeholder="Your name" maxlength="100" required>
        </div>
        <div class="col-md-7">
          <input name="body" class="form-control" placeholder="Write a comment…" maxlength="2000" required>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100">Add</button>
        </div>
        <div id="comment-errors" class="text-danger small mt-2" style="display:none;"></div>
      </form>
    </div>
  </div>
@else
  <p class="text-muted">
    <a href="{{ route('login') }}">Sign in</a> to add a comment.
  </p>
@endauth

<ul id="comments" class="list-group mb-3"></ul>
<button id="load-more" class="btn btn-outline-secondary" style="display:none;">Load more</button>

{{-- Inline JS (simple + robust) --}}
<script>
(function(){
  const issueId   = {{ $issue->id }};
  const csrf      = (document.querySelector('meta[name="csrf-token"]')||{}).getAttribute?.('content') || '';

  // Utility
  const escapeHtml = (s) => (s ?? '').toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

  // =========================
  // COMMENTS: load + paginate
  // =========================
  let nextUrl = `{{ route('issues.comments.index', $issue) }}`;

  async function loadComments(append=false){
    if (!nextUrl) return;
    const res = await fetch(nextUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With':'XMLHttpRequest' }});
    if (!res.ok) return console.error('Failed to load comments');
    const json = await res.json();

    // two possible shapes: {ok, html, next} OR {data, next_page_url}
    const ul = document.getElementById('comments');
    if (!ul) return;

    if (json.html !== undefined) {
      // server rendered partial path
      if (append) ul.insertAdjacentHTML('beforeend', json.html);
      else { ul.innerHTML = json.html; }
      nextUrl = json.next || null;
    } else {
      // JSON items path
      const items = json.data || [];
      const frag = document.createDocumentFragment();
      items.forEach(c => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = `
          <div class="d-flex justify-content-between">
            <strong>${escapeHtml(c.author_name)}</strong>
            <small class="text-muted">${escapeHtml(c.created_at_human ?? c.created_at ?? '')}</small>
          </div>
          <div class="mt-1">${escapeHtml(c.body)}</div>
        `;
        frag.appendChild(li);
      });
      if (append) ul.appendChild(frag); else { ul.innerHTML = ''; ul.appendChild(frag); }
      nextUrl = json.next_page_url || null;
    }

    const btnMore = document.getElementById('load-more');
    if (btnMore) btnMore.style.display = nextUrl ? 'inline-block' : 'none';
  }

  document.getElementById('load-more')?.addEventListener('click', () => loadComments(true));

  // submit comment
  const commentForm = document.getElementById('comment-form');
  if (commentForm) {
    commentForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(commentForm);
      const payload = Object.fromEntries(fd.entries());

      const res = await fetch(`{{ route('issues.comments.store', $issue) }}`, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With':'XMLHttpRequest'
        },
        body: JSON.stringify(payload),
      });

      if (res.ok && (res.status === 200 || res.status === 201)) {
        // reset form, then refresh first page & prepend new comment automatically
        commentForm.reset();
        // simplest: reload the first page and render fresh
        nextUrl = `{{ route('issues.comments.index', $issue) }}`;
        await loadComments(false);

        const err = document.getElementById('comment-errors');
        if (err) { err.style.display='none'; err.innerHTML=''; }
      } else if (res.status === 422) {
        const j = await res.json();
        const err = document.getElementById('comment-errors');
        if (err) {
          err.style.display='block';
          err.innerHTML = Object.values(j.errors || {}).flat().join('<br>');
        }
      } else {
        alert('Failed to add comment');
      }
    });
  }

  // initial load
  loadComments(false);

  // =========================
  // TAGS: attach / detach
  // =========================
  document.getElementById('tag-attach-btn')?.addEventListener('click', async () => {
    const select = document.getElementById('tag-select');
    const tagId  = select?.value;
    if (!tagId) return;

    const res = await fetch(`{{ route('issues.tags.store', $issue) }}`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'Accept':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({ tag_id: Number(tagId) })
    });

    if (!res.ok) return alert('Failed to attach tag');
    const data = await res.json();
    if (data?.tags) renderTags(data.tags);
    select.value = '';
  });

  document.getElementById('tag-chips')?.addEventListener('click', async (e) => {
    const btn = e.target.closest('.tag-detach');
    if (!btn) return;
    const tagId = btn.getAttribute('data-tag-id');
    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'DELETE',
      headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest' }
    });
    if (!res.ok) return alert('Failed to detach tag');
    const data = await res.json();
    if (data?.tags) renderTags(data.tags);
  });

  function renderTags(tags){
    const wrap = document.getElementById('tag-chips');
    if (!wrap) return;
    wrap.innerHTML = '';
    tags.forEach(t => {
      const span = document.createElement('span');
      span.className = 'badge rounded-pill text-bg-secondary me-2 align-middle';
      span.setAttribute('data-tag-id', t.id);
      span.innerHTML = `
        ${t.color ? `<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${escapeHtml(t.color)};margin-right:6px;"></span>` : ''}
        ${escapeHtml(t.name)}
        @can('update', $issue->project)
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="\${t.id}">✕</button>
        @endcan
      `;
      wrap.appendChild(span);
    });
  }

  // =========================
  // ASSIGNEES: attach / detach
  // =========================
  document.getElementById('assignee-attach-btn')?.addEventListener('click', async () => {
    const select = document.getElementById('assignee-select');
    const userId = select?.value;
    if (!userId) return;

    try {
      const res = await fetch(`{{ route('issues.assignees.store', $issue) }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With':'XMLHttpRequest',
        },
        body: JSON.stringify({ user_id: Number(userId) })
      });

      if (!res.ok) {
        const t = await res.text();
        console.error('Assign failed', res.status, t);
        alert(`Failed to assign user (HTTP ${res.status})`);
        return;
      }

      const ct = (res.headers.get('content-type') || '').toLowerCase();
      let data = {};
      if (ct.includes('application/json')) data = await res.json();
      if (data?.assignees) renderAssignees(data.assignees); else location.reload();
      select.value = '';
    } catch (err) {
      console.error(err);
      alert('Network error while assigning user');
    }
  });

  document.getElementById('assignee-chips')?.addEventListener('click', async (e) => {
    const btn = e.target.closest('.assignee-detach');
    if (!btn) return;
    const userId = btn.getAttribute('data-user-id');
    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'DELETE',
      headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest' }
    });
    if (!res.ok) return alert('Failed to remove assignee');
    // optimistic remove; or re-render from server if you prefer
    btn.closest('[data-user-id]')?.remove();
  });

  function renderAssignees(users){
    const wrap = document.getElementById('assignee-chips');
    if (!wrap) return;
    wrap.innerHTML = '';
    users.forEach(u => {
      const span = document.createElement('span');
      span.className = 'badge rounded-pill text-bg-secondary me-2 align-middle';
      span.setAttribute('data-user-id', u.id);
      span.innerHTML = `
        ${escapeHtml(u.name)}
        @can('update', $issue->project)
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="\${u.id}">✕</button>
        @endcan
      `;
      wrap.appendChild(span);
    });
  }
})();
</script>
@endsection
