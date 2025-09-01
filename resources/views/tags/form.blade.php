@php $t = $tag ?? null; @endphp

<div class="mb-3">
  <label for="name" class="form-label">Name</label>
  <input
    id="name"
    name="name"
    type="text"
    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
    value="{{ old('name', $t->name ?? '') }}"
    maxlength="50"
    required
  >
  <x-field-error name="name"/>
</div>

<div class="mb-3">
  <label for="color" class="form-label">Color (hex)</label>
  <input
    id="color"
    name="color"
    type="text"
    class="form-control {{ $errors->has('color') ? 'is-invalid' : '' }}"
    value="{{ old('color', $t->color ?? '') }}"
    placeholder="#6c757d"
    pattern="^#?[0-9A-Fa-f]{6}$"
  >
  <x-field-error name="color"/>
</div>
