{{-- edit.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
  <h1>Edit Issue</h1>
  <form method="POST" action="{{ route('issues.update',$issue) }}">@csrf @method('PUT')
    @include('issues.form', ['issue'=>$issue, 'projects'=>$projects, 'defaults'=>[]])
    <button class="btn btn-primary">Save</button>
    <a class="btn btn-link" href="{{ route('issues.show',$issue) }}">Cancel</a>
  </form>
</div>
@endsection
