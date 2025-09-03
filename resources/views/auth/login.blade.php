@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 420px; margin: 2rem auto;">
    <h1 style="margin-bottom:1rem;">Sign in</h1>

    @if($errors->any())
        <div style="background:#fee; border:1px solid #f99; padding:.75rem; margin-bottom:1rem;">
            <ul style="margin:0; padding-left:1.25rem;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div style="margin-bottom:.75rem;">
            <label for="email">Email</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}" required autofocus
                   style="display:block; width:100%; padding:.5rem; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="margin-bottom:.75rem;">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required
                   style="display:block; width:100%; padding:.5rem; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="margin-bottom:1rem;">
            <label style="display:flex; gap:.5rem; align-items:center;">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit"
            style="padding:.5rem 1rem; border:0; background:#1f6feb; color:#fff; border-radius:4px;">
            Sign in
        </button>
    </form>

    <p style="margin-top:1rem; color:#666;">
        Try: <code>owner@example.com</code> / <code>password</code>
    </p>
</div>
@endsection
