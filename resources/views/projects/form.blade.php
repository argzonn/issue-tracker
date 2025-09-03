@php
    // $project is passed on edit; absent on create
    $p = $project ?? null;

    // format dates for <input type="date">
    $startVal    = old('start_date', optional($p?->start_date)->format('Y-m-d'));
    $deadlineVal = old('deadline',   optional($p?->deadline)->format('Y-m-d'));
@endphp

{{-- Project Name --}}
<div class="mb-3">
  <label for="name" class="form-label">Name</label>
  <input
    id="name"
    name="name"
    class="form-control"
    type="text"
    value="{{ old('name', $p->name ?? '') }}"
    maxlength="255"
    required
  >
  <x-field-error name="name"/>
</div>

{{-- Description --}}
<div class="mb-3">
  <label for="description" class="form-label">Description</label>
  <textarea
    id="description"
    name="description"
    class="form-control"
    rows="3"
    maxlength="20000"
  >{{ old('description', $p->description ?? '') }}</textarea>
  <x-field-error name="description"/>
</div>

<div class="row">
  {{-- Start date --}}
  <div class="col-md-6 mb-3">
    <label for="start_date" class="form-label">Start date</label>
    <input
      id="start_date"
      type="date"
      name="start_date"
      class="form-control"
      value="{{ $startVal }}"
    >
    <x-field-error name="start_date"/>
  </div>

  {{-- Deadline --}}
  <div class="col-md-6 mb-3">
    <label for="deadline" class="form-label">Deadline</label>
    <input
      id="deadline"
      type="date"
      name="deadline"
      class="form-control"
      value="{{ $deadlineVal }}"
    >
    <x-field-error name="deadline"/>
  </div>
</div>

{{-- Visibility toggle (boolean checkbox with hidden fallback) --}}
<div class="form-check mb-3">
  {{-- Important: hidden input ensures "0" is sent when checkbox is unchecked --}}
  <input type="hidden" name="is_public" value="0">
  <input
    class="form-check-input"
    type="checkbox"
    id="is_public"
    name="is_public"
    value="1"
    {{ old('is_public', $p->is_public ?? false) ? 'checked' : '' }}
  >
  <label class="form-check-label" for="is_public">Public project</label>
  <x-field-error name="is_public"/>
</div>
