@extends('layouts.app')

@section('content')
<h1 class="mb-3">New Issue</h1>
<form method="POST" action="{{ route('issues.store') }}">
  @csrf
  @include('issues.form', ['issue' => null, 'projects' => $projects])
  <button class="btn btn-primary">Create</button>
  <a href="{{ route('issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
