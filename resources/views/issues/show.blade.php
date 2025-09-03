@extends('layouts.app')

@section('title', $issue->title)

@section('content')
@php
    use Illuminate\Support\Str;

    // Fallback badge maps (works even if partials are not present)
    $statusClass = [
        'open' => 'success',
        'in_progress' => 'warning',
        'closed' => 'secondary',
    ][$issue->status] ?? 'light';

    $priorityClass = [
        'low' => 'secondary',
        'medium' => 'info',
        'high' => 'danger',
    ][$issue->priority] ?? 'light';

    // If controller didn't pass these, fall back safely (still better to pass from controller)
    $allTags  = $allTags  ?? \App\Models\Tag::query()->orderBy('name')->get(['id','name','color']);
    $allUsers = $allUsers ?? \App\Models\User::query()->orderBy('name')->get(['id','name']);
@endphp

<div class="d-flex align-items-center justify-content-between mb-2">
  <h1 class="mb-0">{{ $issue->title }}</h1>

  <div class="d-flex gap-2">
    {{-- Status --}}
    @php $statusText = Str::headline($issue->status); @endphp
    @includeWhen(View::exists('partials.badge-status'), 'partials.badge-status', ['status' => $issue->status])
    @unless(View::exists('partials.badge-status'))
      <span class="badge text-bg-{{ $statusClass }}" title="Status">{{ $statusText }}</span>
    @endunless

    {{-- Priority --}}
    @php $priorityText = Str::headline($issue->priority); @endphp
    @includeWhen(View::exists('partials.badge-priority'), 'partials.badge-priority', ['priority' => $issue->priority])
    @unless(View::exists('partials.badge-priority'))
      <span class="badge text-bg-{{ $priorityClass }}" title="Priority">{{ $priorityText }}</span>
    @endunless
  </div>
</div>

<p class="text-muted mb-3">
  Project:
  <a href="{{ route('projects.show', $issue->project) }}">{{ e($issue->project->name) }}</a>
  ·
  Due: <strong>{{ optional($issue->due_date)->format('Y-m-d') ?? '—' }}</strong>
</p>

@if($issue->description)
  <div class="mb-4">{!! nl2br(e($issue->description)) !!}</div>
@endif

<hr class="my-4">

{{-- =========================
     TAGS (AJAX attach/detach)
   ========================= --}}
<section class="mb-4">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h4 class="mb-0">Tags</h4>
    @can('update', $issue->project)
      <div class="d-flex gap-2">
        <select id="tag-select" class="form-select form-select-sm">
          <option value="">Add a tag…</option>
          @foreach($allTags as $t)
            <option value="{{ $t->id }}">{{ e($t->name) }}</option>
          @endforeach
        </select>
        <button id="tag-attach-btn" class="btn btn-sm btn-outline-primary" type="button">Attach</button>
      </div>
    @endcan
  </div>

  {{-- Chips container is always present so we can swap its HTML via AJAX --}}
  <div id="tag-chips" class="d-flex flex-wrap gap-2">
    @forelse($issue->tags as $t)
      <span class="badge rounded-pill text-bg-secondary align-middle" data-tag-id="{{ $t->id }}">
        @if($t->color)
          <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $t->color }};margin-right:6px;"></span>
        @endif
        {{ e($t->name) }}
        @can('update', $issue->project)
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="{{ $t->id }}">
            <span class="visually-hidden">Remove tag</span>✕
          </button>
        @endcan
      </span>
    @empty
      <span class="text-muted">No tags</span>
    @endforelse
  </div>

  <div id="tag-errors" class="text-danger small mt-2 d-none"></div>
</section>

<hr class="my-4">

{{-- =========================
     ASSIGNEES (Bonus, AJAX attach/detach)
   ========================= --}}
<section class="mb-4">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h4 class="mb-0">Assignees</h4>
    @can('update', $issue->project)
      <div class="d-flex gap-2">
        <select id="assignee-select" class="form-select form-select-sm">
          <option value="">Assign a user…</option>
          @foreach($allUsers as $u)
            <option value="{{ $u->id }}">{{ e($u->name) }}</option>
          @endforeach
        </select>
        <button id="assignee-attach-btn" class="btn btn-sm btn-outline-primary" type="button">Assign</button>
      </div>
    @endcan
  </div>

  <div id="assignee-chips" class="d-flex flex-wrap gap-2">
    @forelse($issue->assignees as $u)
      <span class="badge rounded-pill text-bg-secondary align-middle" data-user-id="{{ $u->id }}">
        {{ e($u->name) }}
        @can('update', $issue->project)
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="{{ $u->id }}">
            <span class="visually-hidden">Remove assignee</span>✕
          </button>
        @endcan
      </span>
    @empty
      <span class="text-muted">No assignees</span>
    @endforelse
  </div>

  <div id="assignee-errors" class="text-danger small mt-2 d-none"></div>
</section>

<hr class="my-4">

{{-- =========================
     COMMENTS (AJAX list + add; prepend on add)
   ========================= --}}
<section class="mb-4">
  <h4 class="mb-2">Comments</h4>

  @auth
    <div class="card mb-3">
      <div class="card-body">
        <form id="comment-form" class="row g-2" action="{{ route('issues.comments.store', $issue) }}" method="post">
          @csrf
          <div class="col-md-3">
            <input name="author_name" class="form-control" placeholder="Your name" maxlength="80" required>
          </div>
          <div class="col-md-7">
            <input name="body" class="form-control" placeholder="Write a comment…" maxlength="2000" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Add</button>
          </div>
          <div id="comment-errors" class="text-danger small mt-2 d-none"></div>
        </form>
      </div>
    </div>
  @else
    <p class="text-muted">
      <a href="{{ route('login') }}">Sign in</a> to add a comment.
    </p>
  @endauth

  <div id="comments-list"></div>
  <button id="btn-load-more" class="btn btn-outline-secondary d-none" type="button">Load more</button>
</section>
@endsection

@push('scripts')
<script>
(function() {
  const issueId = {{ $issue->id }};
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const $  = (sel, el=document) => el.querySelector(sel);
  const $$ = (sel, el=document) => Array.from(el.querySelectorAll(sel));

  // -----------------------
  // COMMENTS: load & add
  // -----------------------
  const commentsList = $('#comments-list');
  const moreBtn      = $('#btn-load-more');
  let   nextUrl      = "{{ route('issues.comments.index', $issue) }}";

  async function loadComments(url, append=false) {
    if (!url) return;
    const res  = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }});
    if (!res.ok) return;
    const data = await res.json();

    if (typeof data.html === 'string') {
      if (append) commentsList.insertAdjacentHTML('beforeend', data.html);
      else        commentsList.innerHTML = data.html;
      nextUrl = data.next || null;
      moreBtn.classList.toggle('d-none', !nextUrl);
    }
  }

  // initial page of comments
  loadComments(nextUrl);

  moreBtn?.addEventListener('click', () => nextUrl && loadComments(nextUrl, true));

  $('#comment-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form  = e.currentTarget;
    const errEl = $('#comment-errors');

    const res = await fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
      body: new FormData(form)
    });

    if (res.status === 201) {
      const data = await res.json();
      if (typeof data.html === 'string') {
        commentsList.insertAdjacentHTML('afterbegin', data.html); // prepend
      }
      form.reset();
      errEl?.classList.add('d-none');
      if (errEl) errEl.textContent = '';
    } else if (res.status === 422) {
      const j = await res.json();
      const msgs = Object.values(j.errors || {}).flat();
      if (errEl) {
        errEl.textContent = msgs.join(' ');
        errEl.classList.remove('d-none');
      }
    } else if (res.status === 429) {
      if (errEl) {
        errEl.textContent = 'You are commenting too fast. Try again shortly.';
        errEl.classList.remove('d-none');
      }
    } else {
      if (errEl) {
        errEl.textContent = 'Failed to add comment.';
        errEl.classList.remove('d-none');
      }
    }
  });

  // -----------------------
  // TAGS: attach / detach
  // -----------------------
  const tagErrors = $('#tag-errors');
  $('#tag-attach-btn')?.addEventListener('click', async () => {
    const select = $('#tag-select');
    const tagId  = select?.value;
    if (!tagId) return;

    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });
    try {
      const data = await res.json();
      if (res.ok && typeof data.html === 'string') {
        $('#tag-chips').innerHTML = data.html;
        select.value = '';
        tagErrors?.classList.add('d-none');
      } else {
        throw new Error();
      }
    } catch {
      if (tagErrors) { tagErrors.textContent = 'Failed to attach tag.'; tagErrors.classList.remove('d-none'); }
    }
  });

  $('#tag-chips')?.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.tag-detach');
    if (!btn) return;
    const tagId = btn.getAttribute('data-tag-id');

    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'DELETE',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });
    try {
      const data = await res.json();
      if (res.ok && typeof data.html === 'string') {
        $('#tag-chips').innerHTML = data.html;
        tagErrors?.classList.add('d-none');
      } else {
        throw new Error();
      }
    } catch {
      if (tagErrors) { tagErrors.textContent = 'Failed to detach tag.'; tagErrors.classList.remove('d-none'); }
    }
  });

  // -----------------------
  // ASSIGNEES: attach / detach (bonus)
  // -----------------------
  const assigneeErrors = $('#assignee-errors');

  $('#assignee-attach-btn')?.addEventListener('click', async () => {
    const select = $('#assignee-select');
    const userId = select?.value;
    if (!userId) return;

    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#assignee-chips').innerHTML = data.html;
      select.value = '';
      assigneeErrors?.classList.add('d-none');
    } else {
      if (assigneeErrors) { assigneeErrors.textContent = 'Failed to assign user.'; assigneeErrors.classList.remove('d-none'); }
    }
  });

  $('#assignee-chips')?.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.assignee-detach');
    if (!btn) return;
    const userId = btn.getAttribute('data-user-id');

    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'DELETE',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#assignee-chips').innerHTML = data.html;
      assigneeErrors?.classList.add('d-none');
    } else {
      if (assigneeErrors) { assigneeErrors.textContent = 'Failed to remove assignee.'; assigneeErrors.classList.remove('d-none'); }
    }
  });

})();
</script>
@endpush
