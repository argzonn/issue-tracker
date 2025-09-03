@extends('layouts.app')

@section('content')
@php
    // enums (values must stay lowercase to pass validation)
    $statuses = [
        'open'        => 'Open',
        'in_progress' => 'In progress',
        'closed'      => 'Closed',
    ];
    $priorities = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    $currentStatus   = old('status', $defaults['status'] ?? 'open');
    $currentPriority = old('priority', $defaults['priority'] ?? 'medium');
@endphp

<h1 class="mb-3">New Issue</h1>

<form method="POST" action="{{ route('issues.store') }}" novalidate>
  @csrf

  <div class="mb-3">
    <label class="form-label">Project</label>
    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
      <option value="">— Select project —</option>
      @foreach($projects as $p)
        <option value="{{ $p->id }}" @selected(old('project_id') == $p->id)>{{ $p->name }}</option>
      @endforeach
    </select>
    @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Title</label>
    <input name="title" type="text" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title') }}" maxlength="200" required>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror"
              maxlength="5000">{{ old('description') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="status" class="form-select @error('status') is-invalid @enderror" required>
        @foreach($statuses as $val => $label)
          <option value="{{ $val }}" @selected($currentStatus === $val)>{{ $label }}</option>
        @endforeach
      </select>
      @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
      <label class="form-label">Priority</label>
      <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
        @foreach($priorities as $val => $label)
          <option value="{{ $val }}" @selected($currentPriority === $val)>{{ $label }}</option>
        @endforeach
      </select>
      @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
      <label class="form-label">Due date</label>
      <input name="due_date" type="date" class="form-control @error('due_date') is-invalid @enderror"
             value="{{ old('due_date') }}">
      @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="mt-4">
    <button class="btn btn-primary">Create</button>
    <a href="{{ route('issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>
@endsection
