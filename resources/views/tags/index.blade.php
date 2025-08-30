@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px;">
  <h1 class="mb-3">Tags</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('tags.store') }}" class="row g-2 mb-4" autocomplete="off">
    @csrf
    <div class="col-md-5">
      <input name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
      <div class="input-group">
        <span class="input-group-text">#</span>
        <input name="color" class="form-control" placeholder="ff9900 (optional)" value="{{ ltrim(old('color',''), '#') }}">
      </div>
      <div class="form-text">3 or 6 hex digits. “#” optional — we’ll normalize.</div>
      @error('color')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Add</button>
    </div>
  </form>

  <table class="table align-middle">
    <thead>
      <tr>
        <th style="width:40%;">Name</th>
        <th style="width:30%;">Color</th>
        <th style="width:30%;" class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($tags as $t)
        <tr>
          <td>{{ $t->name }}</td>
          <td>
            @if($t->color)
              <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $t->color }};vertical-align:middle;margin-right:6px;"></span>
              <code>{{ $t->color }}</code>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('tags.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ route('tags.destroy', $t) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Delete this tag? This cannot be undone.')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-center text-muted">No tags yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $tags->links() }}
</div>
@endsection
