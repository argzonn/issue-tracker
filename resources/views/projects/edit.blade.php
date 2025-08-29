@extends('layouts.app')
@section('content')
<div class="container" style="max-width:720px;">
  <h1>Edit Project</h1>
  <form method="POST" action="{{ route('projects.update',$project) }}">@csrf @method('PUT')
    @include('projects.form',['project'=>$project])
    <button class="btn btn-primary">Save</button>
    <a class="btn btn-link" href="{{ route('projects.show',$project) }}">Cancel</a>
  </form>
</div>
@endsection
