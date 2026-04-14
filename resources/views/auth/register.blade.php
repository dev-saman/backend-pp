@extends('layouts.auth')

@section('title', 'Register - AdvantageHCS Admin')

@section('content')
<div class="auth-card">
    <h2>Create Admin Account</h2>
    <p>Set up your administrator account</p>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Smith" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@example.com" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number <span style="color:#9ca3af">(optional)</span></label>
            <div class="input-wrap">
                <i class="fas fa-phone"></i>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>
        </div>

        <button type="submit" class="btn-auth" style="margin-top:8px;">
            Create Account
        </button>
    </form>
</div>

<div class="auth-footer">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
