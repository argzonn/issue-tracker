@php
    // $project is passed on edit; absent on create
    $p = $project ?? null;

    // format dates for <input type="date">
    $startVal   = old('start_date', optional($p?->start_date)->format('Y-m-d'));
    $deadlineVal= old('deadline',   optional($p?->deadline)->format('Y-m-d'));
@endphp

<div class="mb-3">
  <label class="form-label">Name</label>
  <input name="name" class="form-control" value="{{ old('name', $p->name ?? '') }}">
  @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="description" class="form-control" rows="3">{{ old('description', $p->description ?? '') }}</textarea>
  @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Start date</label>
  <input type="date" name="start_date" class="form-control" value="{{ $startVal }}">
  @error('start_date')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label">Deadline</label>
  <input type="date" name="deadline" class="form-control" value="{{ $deadlineVal }}">
  @error('deadline')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

{{-- Visibility toggle (Option C) --}}
<div class="form-check mb-3">
  <input
    class="form-check-input"
    type="checkbox"
    id="is_public"
    name="is_public"
    value="1"
    {{ old('is_public', $p->is_public ?? false) ? 'checked' : '' }}
  >
  <label class="form-check-label" for="is_public">Public project</label>
  @error('is_public')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
