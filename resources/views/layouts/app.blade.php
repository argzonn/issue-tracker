<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}"><meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/js/app.js', 'resources/css/app.css'])
  <title>Issue Tracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light mb-3">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="{{ route('projects.index') }}">Issue Tracker</a>
    <div class="d-flex gap-2 align-items-center">
      @auth
        <span class="me-2">Hello, {{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login</a>
      @endauth

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- prints page-level scripts pushed from views --}}
@stack('scripts')
</body>
</html>
