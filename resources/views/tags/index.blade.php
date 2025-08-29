@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
  <h1>Tags</h1>

  <form method="POST" action="{{ route('tags.store') }}" class="row g-2 mb-3">@csrf
    <div class="col-md-5">
      <input name="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
      <input name="color" class="form-control" placeholder="#RRGGBB (optional)" value="{{ old('color') }}">
      @error('color')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Add</button>
    </div>
  </form>

  <table class="table">
    <thead><tr><th>Name</th><th>Color</th></tr></thead>
    <tbody>
      @foreach($tags as $t)
      <tr>
        <td>{{ $t->name }}</td>
        <td>
          @if($t->color)
            <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $t->color }};"></span>
            <code>{{ $t->color }}</code>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $tags->links() }}
</div>
@endsection
