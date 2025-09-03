<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF for forms & AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- App assets (Tailwind/Bootstrap overrides, your JS) --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    {{-- Bootstrap (if you’re relying on it in views) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>
        @hasSection('title')
            @yield('title') — Issue Tracker
        @else
            Issue Tracker
        @endif
    </title>
</head>
<body>
<nav class="navbar navbar-light bg-light mb-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="{{ route('projects.index') }}">Issue Tracker</a>

        <div class="d-flex align-items-center gap-3">

            {{-- Primary nav --}}
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-link p-0" href="{{ route('projects.index') }}">Projects</a>
                <a class="btn btn-link p-0" href="{{ route('issues.index') }}">Issues</a>
                <a class="btn btn-link p-0" href="{{ route('tags.index') }}">Tags</a>
            </div>

            {{-- Auth controls --}}
            @auth
                <span class="text-muted">Hello, {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Page-level scripts pushed from views --}}
@stack('scripts')
</body>
</html>
