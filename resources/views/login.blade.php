@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <h1>LOGIN</h1>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        <label for="login-username">Username</label>
        <input id="login-username" type="text" name="username" placeholder="Username" value="{{ old('username') }}" autocomplete="username" autofocus>

        <label for="login-password">Password</label>
        <input id="login-password" type="password" name="password" placeholder="Password" autocomplete="current-password">

        <p class="err">
            @if ($errors->has('unregistered'))
                {{ $errors->first('unregistered') }} <a href="{{ route('register') }}">Daftar sekarang</a>
            @else
                {{ $errors->first() }}
            @endif
        </p>

        <button id="login-submit" type="submit">LOGIN</button>
        <p class="switch">belum punya akun? <a id="to-register" href="{{ route('register') }}">Daftar</a></p>
        <div style="margin-top: 8px; font-size: 11px; color: #475569; text-align: center; padding: 10px;">
            <strong style="display: block; margin-bottom: 6px; color: #1e293b;">Demo Account</strong>
            Username: <strong>citra</strong><br>
            Password: <strong>password123</strong>
        </div>
    </form>
@endsection