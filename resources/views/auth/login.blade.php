@extends('layouts.auth')

@section('title', 'Login - AdvantageHCS Admin')

@section('content')
<div class="auth-card">
    <h2>Welcome back</h2>
    <p>Sign in to your admin account</p>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="remember-row">
            <label>
                <input type="checkbox" name="remember">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn-auth">
            Sign In
        </button>
    </form>
</div>

<div class="auth-footer">
    Don't have an account? <a href="{{ route('register') }}">Create one</a>
</div>
@endsection
