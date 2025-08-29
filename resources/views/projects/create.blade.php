@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
  <h1>New Project</h1>
  <form method="POST" action="{{ route('projects.store') }}">@csrf
    @include('projects.form')
    <button class="btn btn-primary">Create</button>
    <a class="btn btn-link" href="{{ route('projects.index') }}">Cancel</a>
  </form>
</div>
@endsection
