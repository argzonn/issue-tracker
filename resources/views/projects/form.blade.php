@php($p = $project ?? null)
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
  <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $p->start_date ?? '') }}">
  @error('start_date')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label">Deadline</label>
  <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $p->deadline ?? '') }}">
  @error('deadline')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
