@extends('layouts.app')

@section('content')
<h1 class="mb-1">{{ $issue->title }}</h1>
<p class="text-muted mb-3">
  Project: <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a> ·
  Status: @statusBadge($issue->status) ·
  Priority: @priorityBadge($issue->priority) ·
  Due: <strong>{{ $issue->due_date }}</strong>
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
      <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="{{ $t->id }}">✕</button>
    </span>
  @endforeach
</div>

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

<hr class="my-4">

{{-- ASSIGNEES (chips) --}}
<h4 class="mb-2">Assignees</h4>
<div id="assignee-chips" class="mb-3">
  @foreach($issue->assignees as $u)
    <span class="badge rounded-pill text-bg-secondary me-2 align-middle" data-user-id="{{ $u->id }}">
      {{ $u->name }}
      <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="{{ $u->id }}">✕</button>
    </span>
  @endforeach
</div>

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

<hr class="my-4">

{{-- COMMENTS --}}
<h4 class="mb-2">Comments</h4>

<div class="card mb-3">
  <div class="card-body">
    <form id="comment-form" class="row g-2">
      @csrf
      <div class="col-md-3">
        <input name="author_name" class="form-control" placeholder="Your name">
      </div>
      <div class="col-md-7">
        <input name="body" class="form-control" placeholder="Write a comment…">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Add</button>
      </div>
      <div id="comment-errors" class="text-danger small mt-2" style="display:none;"></div>
    </form>
  </div>
</div>

<ul id="comments" class="list-group mb-3"></ul>
<button id="load-more" class="btn btn-outline-secondary" style="display:none;">Load more</button>

{{-- Inline JS (no Vite needed) --}}
<script>
(function(){
  const issueId   = {{ $issue->id }};
  const csrf      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // -----------------------------
  // COMMENTS: load + paginate
  // -----------------------------
  let nextUrl = `{{ route('issues.comments.index', $issue) }}`;

  async function loadComments(append=false){
    if(!nextUrl){ return; }
    const res = await fetch(nextUrl, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
    if(!res.ok){ console.error('Failed to load comments'); return; }
    const json = await res.json();

    const ul = document.getElementById('comments');
    const items = json.data || [];
    const frag = document.createDocumentFragment();

    items.forEach(c => {
      const li = document.createElement('li');
      li.className = 'list-group-item';
      li.innerHTML = `
        <div class="d-flex justify-content-between">
          <strong>${escapeHtml(c.author_name)}</strong>
          <small class="text-muted">${escapeHtml(c.created_at_human)}</small>
        </div>
        <div>${escapeHtml(c.body)}</div>
      `;
      frag.appendChild(li);
    });

    if(append){ ul.appendChild(frag); } else { ul.innerHTML=''; ul.appendChild(frag); }
    nextUrl = json.next_page_url;
    document.getElementById('load-more').style.display = nextUrl ? 'inline-block' : 'none';
  }

  document.getElementById('load-more').addEventListener('click', function(){
    loadComments(true);
  });

  // -----------------------------
  // COMMENTS: submit
  // -----------------------------
  document.getElementById('comment-form').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = e.currentTarget;
    const body = {
      author_name: form.author_name.value.trim(),
      body: form.body.value.trim(),
    };
    const res = await fetch(`{{ route('issues.comments.store', $issue) }}`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify(body)
    });

    if(res.status === 201){
      form.reset();
      // reload from first page to show newest
      nextUrl = `{{ route('issues.comments.index', $issue) }}`;
      loadComments(false);
      document.getElementById('comment-errors').style.display='none';
      document.getElementById('comment-errors').innerHTML='';
    } else if(res.status === 422){
      const j = await res.json();
      const errBox = document.getElementById('comment-errors');
      errBox.style.display='block';
      errBox.innerHTML = Object.values(j.errors||{}).flat().join('<br>');
    } else {
      alert('Failed to add comment');
    }
  });

  // -----------------------------
  // TAGS: attach
  // -----------------------------
  document.getElementById('tag-attach-btn').addEventListener('click', async function(){
    const select = document.getElementById('tag-select');
    const tagId  = select.value;
    if(!tagId) return;

    const res = await fetch(`{{ route('issues.tags.attach', $issue) }}`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({ tag_id: tagId })
    });

    if(res.ok){
      const j = await res.json();
      renderTags(j.tags || []);
      select.value = '';
    } else {
      alert('Failed to attach tag');
    }
  });

  // -----------------------------
  // TAGS: detach (delegation)
  // -----------------------------
  document.getElementById('tag-chips').addEventListener('click', async function(e){
    if(!e.target.classList.contains('tag-detach')) return;
    const tagId = e.target.getAttribute('data-tag-id');
    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest' }
    });
    if(res.ok){
      const j = await res.json();
      renderTags(j.tags || []);
    } else {
      alert('Failed to detach tag');
    }
  });

  function renderTags(tags){
    const wrap = document.getElementById('tag-chips');
    wrap.innerHTML = '';
    tags.forEach(t => {
      const span = document.createElement('span');
      span.className = 'badge rounded-pill text-bg-secondary me-2 align-middle';
      span.setAttribute('data-tag-id', t.id);
      span.innerHTML = `
        ${t.color ? `<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${escapeHtml(t.color)};margin-right:6px;"></span>` : ''}
        ${escapeHtml(t.name)}
        <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="${t.id}">✕</button>
      `;
      wrap.appendChild(span);
    });
  }

  // -----------------------------
  // ASSIGNEES: attach
  // -----------------------------
  document.getElementById('assignee-attach-btn').addEventListener('click', async function(){
    const select = document.getElementById('assignee-select');
    const userId = select.value;
    if(!userId) return;

    const res = await fetch(`{{ route('issues.assignees.attach', $issue) }}`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({ user_id: userId })
    });

    if(res.ok){
      const j = await res.json();
      renderAssignees(j.assignees || []);
      select.value = '';
    } else {
      alert('Failed to assign user');
    }
  });

  // -----------------------------
  // ASSIGNEES: detach (delegation)
  // -----------------------------
  document.getElementById('assignee-chips').addEventListener('click', async function(e){
    if(!e.target.classList.contains('assignee-detach')) return;
    const userId = e.target.getAttribute('data-user-id');
    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With':'XMLHttpRequest' }
    });
    if(res.ok){
      // optimistic update; or re-render from server if you prefer
      e.target.closest('[data-user-id]').remove();
    } else {
      alert('Failed to remove assignee');
    }
  });

  function renderAssignees(users){
    const wrap = document.getElementById('assignee-chips');
    wrap.innerHTML = '';
    users.forEach(u => {
      const span = document.createElement('span');
      span.className = 'badge rounded-pill text-bg-secondary me-2 align-middle';
      span.setAttribute('data-user-id', u.id);
      span.innerHTML = `
        ${escapeHtml(u.name)}
        <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="${u.id}">✕</button>
      `;
      wrap.appendChild(span);
    });
  }

  function escapeHtml(s){ return (s??'').toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }

  // initial load
  loadComments(false);
})();
</script>
@endsection
