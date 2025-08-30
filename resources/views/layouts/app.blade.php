<!doctype html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Issue Tracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light mb-3">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="{{ route('projects.index') }}">Issue Tracker</a>
    <div>
      <a class="btn btn-link" href="{{ route('projects.index') }}">Projects</a>
      <a class="btn btn-link" href="{{ route('issues.index') }}">Issues</a>
      <a class="btn btn-link" href="{{ route('tags.index') }}">Tags</a>
    </div>
  </div>
</nav>
<div class="container">
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @yield('content')
</div>
</body>
</html>
