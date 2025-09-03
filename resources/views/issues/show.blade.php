@extends('layouts.app')

@section('title', $issue->title)

@section('content')
@php
    use Illuminate\Support\Str;

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

    // Fallbacks if controller didn't pass them
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

  <div id="tag-chips" class="d-flex flex-wrap gap-2">
    @forelse($issue->tags as $t)
      <span class="badge rounded-pill text-bg-secondary align-middle" data-tag-id="{{ $t->id }}">
        @if($t->color)
          <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $t->color }};margin-right:6px;"></span>
        @endif
        {{ e($t->name) }}
        @can('update', $issue->project)
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach" data-tag-id="{{ $t->id }}" aria-label="Remove tag">✕</button>
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
          <button type="button" class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline assignee-detach" data-user-id="{{ $u->id }}" aria-label="Remove assignee">✕</button>
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
     COMMENTS (server render + AJAX add; prepend on add)
   ========================= --}}
<section class="mb-4">
  <h4 class="mb-2">Comments</h4>

  @auth
    <div class="card mb-3">
      <div class="card-body">
        <form id="commentForm" class="row g-2" action="{{ route('issues.comments.store', $issue) }}" method="post">
          @csrf
          <div class="col-md-3">
            <input name="author_name" class="form-control" placeholder="Your name" maxlength="100" required>
          </div>
          <div class="col-md-7">
            <input name="body" class="form-control" placeholder="Write a comment…" maxlength="2000" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Add</button>
          </div>
          <div id="commentError" class="text-danger small mt-2 d-none"></div>
        </form>
      </div>
    </div>
  @else
    <p class="text-muted">
      <a href="{{ route('login') }}">Sign in</a> to add a comment.
    </p>
  @endauth

  <ul id="commentsList" class="list-group">
    @foreach($issue->comments()->latest()->paginate(10) as $comment)
      @include('issues.partials._comment', ['comment' => $comment])
    @endforeach
  </ul>
  {{-- If you want pagination links for non-AJAX navigation, you could show them here --}}
</section>
@endsection

@push('scripts')
<script>
(function() {
  const issueId = {{ $issue->id }};
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

  const $  = (sel, el=document) => el.querySelector(sel);

  // -----------------------
  // COMMENTS: AJAX add
  // -----------------------
  const commentsList = $('#commentsList');
  const form = $('#commentForm');
  const errorBox = $('#commentError');

  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (errorBox) { errorBox.classList.add('d-none'); errorBox.textContent = ''; }

    const fd = new FormData(form);

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: fd
      });

      if (res.status === 422) {
        const j = await res.json();
        const first = Object.values(j.errors || {}).flat()[0] || 'Validation error.';
        throw new Error(first);
      }

      if (!res.ok) {
        const j = await res.json().catch(() => ({}));
        throw new Error(j.message || 'Failed to add comment.');
      }

      const data = await res.json();
      if (data.ok && typeof data.html === 'string') {
        const t = document.createElement('template');
        t.innerHTML = data.html.trim();
        const node = t.content.firstChild;
        commentsList.prepend(node);        // PREPEND new comment
        form.reset();                      // clear inputs
      } else {
        throw new Error('Unexpected server response.');
      }
    } catch (err) {
      if (errorBox) {
        errorBox.textContent = err.message || 'Failed to add comment.';
        errorBox.classList.remove('d-none');
      }
    }
  });

  // -----------------------
  // TAGS: attach / detach
  // -----------------------
  const tagErrors = $('#tag-errors');
  $('#tag-attach-btn') && $('#tag-attach-btn').addEventListener('click', async () => {
    const select = $('#tag-select'); const tagId = select && select.value;
    if (!tagId) return;

    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#tag-chips').innerHTML = data.html;
      select.value = '';
      tagErrors && tagErrors.classList.add('d-none');
    } else {
      tagErrors && (tagErrors.textContent = 'Failed to attach tag.', tagErrors.classList.remove('d-none'));
    }
  });

  $('#tag-chips') && $('#tag-chips').addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.tag-detach'); if (!btn) return;
    const tagId = btn.getAttribute('data-tag-id');

    const res = await fetch(`/issues/${issueId}/tags/${tagId}`, {
      method: 'DELETE',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#tag-chips').innerHTML = data.html;
      tagErrors && tagErrors.classList.add('d-none');
    } else {
      tagErrors && (tagErrors.textContent = 'Failed to detach tag.', tagErrors.classList.remove('d-none'));
    }
  });

  // -----------------------
  // ASSIGNEES: attach / detach (bonus)
  // -----------------------
  const assigneeErrors = $('#assignee-errors');

  $('#assignee-attach-btn') && $('#assignee-attach-btn').addEventListener('click', async () => {
    const select = $('#assignee-select'); const userId = select && select.value;
    if (!userId) return;

    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#assignee-chips').innerHTML = data.html;
      select.value = '';
      assigneeErrors && assigneeErrors.classList.add('d-none');
    } else {
      assigneeErrors && (assigneeErrors.textContent = 'Failed to assign user.', assigneeErrors.classList.remove('d-none'));
    }
  });

  $('#assignee-chips') && $('#assignee-chips').addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.assignee-detach'); if (!btn) return;
    const userId = btn.getAttribute('data-user-id');

    const res = await fetch(`/issues/${issueId}/assignees/${userId}`, {
      method: 'DELETE',
      headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
    });

    let data = null; try { data = await res.json(); } catch {}
    if (res.ok && data && typeof data.html === 'string') {
      $('#assignee-chips').innerHTML = data.html;
      assigneeErrors && assigneeErrors.classList.add('d-none');
    } else {
      assigneeErrors && (assigneeErrors.textContent = 'Failed to remove assignee.', assigneeErrors.classList.remove('d-none'));
    }
  });
})();
</script>
@endpush
