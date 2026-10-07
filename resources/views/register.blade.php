@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <h1>DAFTAR</h1>

        <label for="register-email">Email</label>
        <input id="register-email" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email" autofocus>

        <label for="register-username">Username</label>
        <input id="register-username" type="text" name="username" placeholder="Username" value="{{ old('username') }}" autocomplete="username">

        <label for="register-password">Password</label>
        <input id="register-password" type="password" name="password" placeholder="Password" autocomplete="new-password">

        <p class="err">{{ $errors->first() }}</p>

        <button id="register-submit" type="submit">DAFTAR</button>
        <p class="switch">sudah punya akun? <a id="to-login" href="{{ route('login') }}">Login</a></p>
    </form>
@endsection
