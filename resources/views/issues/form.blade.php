@php($i = $issue ?? null)
<div class="mb-3">
  <label class="form-label">Project</label>
  <select name="project_id" class="form-select">
    @foreach($projects as $p)
      <option value="{{ $p->id }}" @selected(old('project_id', $i->project_id ?? '')==$p->id)>{{ $p->name }}</option>
    @endforeach
  </select>
  @error('project_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label">Title</label>
  <input name="title" class="form-control" value="{{ old('title', $i->title ?? '') }}">
  @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="description" rows="4" class="form-control">{{ old('description', $i->description ?? '') }}</textarea>
  @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Status</label>
    @php($status = old('status', $i->status ?? ($defaults['status'] ?? 'open')))
    <select name="status" class="form-select">
      @foreach(['open','in_progress','closed'] as $s)
        <option value="{{ $s }}" @selected($status===$s)>{{ $s }}</option>
      @endforeach
    </select>
    @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Priority</label>
    @php($priority = old('priority', $i->priority ?? ($defaults['priority'] ?? 'medium')))
    <select name="priority" class="form-select">
      @foreach(['low','medium','high'] as $p)
        <option value="{{ $p }}" @selected($priority===$p)>{{ $p }}</option>
      @endforeach
    </select>
    @error('priority')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Due date</label>
    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($i->due_date ?? null)->format('Y-m-d')) }}">
    @error('due_date')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
</div>
