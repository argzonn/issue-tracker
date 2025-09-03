@extends('layouts.app')

@section('content')
<h1 class="mb-3">Edit Tag</h1>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('tags.update', $tag) }}" class="row g-3" autocomplete="off">
  @csrf
  @method('PUT')

  <div class="col-md-6">
    <label class="form-label">Name</label>
    <input name="name" class="form-control" value="{{ old('name', $tag->name) }}" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Color (hex)</label>
    <div class="input-group">
      <span class="input-group-text">#</span>
      <input name="color" class="form-control" value="{{ ltrim(old('color', $tag->color ?? ''), '#') }}" placeholder="ff9900">
      <span class="input-group-text" style="background: {{ $tag->color ?? '#ffffff' }}; width: 2.5rem">&nbsp;</span>
    </div>
    <div class="form-text">Use 3 or 6 hex digits. The “#” is optional.</div>
  </div>

  <div class="col-12 d-flex gap-2 mt-2">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>

<form method="POST" action="{{ route('tags.destroy', $tag) }}" class="mt-4"
      onsubmit="return confirm('Delete this tag? This cannot be undone.')">
  @csrf
  @method('DELETE')
  <button class="btn btn-outline-danger">Delete tag</button>
</form>
@endsection
