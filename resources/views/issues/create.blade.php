{{-- create.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
  <h1>New Issue</h1>
  <form method="POST" action="{{ route('issues.store') }}">@csrf
    @include('issues.form', ['issue'=>null, 'projects'=>$projects, 'defaults'=>$defaults])
    <button class="btn btn-primary">Create</button>
    <a class="btn btn-link" href="{{ route('issues.index') }}">Cancel</a>
  </form>
</div>
@endsection
