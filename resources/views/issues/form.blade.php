@php
  /** @var \App\Models\Issue|null $issue */
  $i = $issue ?? null;
@endphp

<div class="mb-3">
  <label for="project_id" class="form-label">Project</label>
  <select
    id="project_id"
    name="project_id"
    class="form-select {{ $errors->has('project_id') ? 'is-invalid' : '' }}"
    required
  >
    <option value="">Select project…</option>
    @foreach($projects as $p)
      <option value="{{ $p->id }}" @selected((int)old('project_id', (int)($i->project_id ?? 0)) === $p->id)>
        {{ $p->name }}
      </option>
    @endforeach
  </select>
  <x-field-error name="project_id"/>
</div>

<div class="mb-3">
  <label for="title" class="form-label">Title</label>
  <input
    id="title"
    name="title"
    type="text"
    class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
    value="{{ old('title', $i->title ?? '') }}"
    maxlength="255"
    required
  >
  <x-field-error name="title"/>
</div>

<div class="mb-3">
  <label for="description" class="form-label">Description</label>
  <textarea
    id="description"
    name="description"
    rows="4"
    class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
  >{{ old('description', $i->description ?? '') }}</textarea>
  <x-field-error name="description"/>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label for="status" class="form-label">Status</label>
    <select
      id="status"
      name="status"
      class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}"
      required
    >
      @foreach(['open'=>'Open','in_progress'=>'In Progress','closed'=>'Closed'] as $val => $label)
        <option value="{{ $val }}" @selected(old('status', $i->status ?? 'open') === $val)>{{ $label }}</option>
      @endforeach
    </select>
    <x-field-error name="status"/>
  </div>

  <div class="col-md-4 mb-3">
    <label for="priority" class="form-label">Priority</label>
    <select
      id="priority"
      name="priority"
      class="form-select {{ $errors->has('priority') ? 'is-invalid' : '' }}"
      required
    >
      @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $val => $label)
        <option value="{{ $val }}" @selected(old('priority', $i->priority ?? 'medium') === $val)>{{ $label }}</option>
      @endforeach
    </select>
    <x-field-error name="priority"/>
  </div>

  <div class="col-md-4 mb-3">
    <label for="due_date" class="form-label">Due date</label>
    <input
      id="due_date"
      name="due_date"
      type="date"
      class="form-control {{ $errors->has('due_date') ? 'is-invalid' : '' }}"
      value="{{ old('due_date', optional($i?->due_date)->format('Y-m-d')) }}"
    >
    <x-field-error name="due_date"/>
  </div>
</div>
