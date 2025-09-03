@extends('layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-3">New Issue</h1>
  <form method="GET" action="{{ route('projects.issues.create', ['project' => 0]) }}"
        onsubmit="event.preventDefault(); const id=document.getElementById('proj').value; if(id){ window.location = '{{ url('projects') }}/'+id+'/issues/create'; }">
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">Choose a project</label>
        <select id="proj" class="form-select" required>
          <option value="">— Select —</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ e($p->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary w-100" type="submit">Continue</button>
      </div>
    </div>
  </form>
</div>
@endsection
