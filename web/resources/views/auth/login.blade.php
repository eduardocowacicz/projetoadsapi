@extends('layouts.auth')

@section('content')
    <h1>Login</h1>
    <p>Use seu email e senha para acessar o painel.</p>

    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="message error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Senha</label>
            <input id="password" name="password" type="password" required>
            @error('password')
                <div class="message error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Entrar</button>
    </form>
@endsection
