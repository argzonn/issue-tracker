@extends('layouts.app')

@section('content')
<h1 class="mb-3">Edit Issue</h1>
<form method="POST" action="{{ route('issues.update', $issue) }}">
  @csrf
  @method('PUT')
  @include('issues.form', ['issue' => $issue, 'projects' => $projects])
  <button class="btn btn-primary">Save changes</button>
  <a href="{{ route('issues.show', $issue) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
