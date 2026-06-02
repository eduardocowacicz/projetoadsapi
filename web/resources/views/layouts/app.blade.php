<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistema de Eventos' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="app">
        <header class="topbar">
            <div class="brand">Sistema de Eventos</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('eventos.page') }}">Eventos</a>
                <a href="{{ route('participantes.page') }}">Participantes</a>
                <a href="{{ route('inscricoes.page') }}">Inscricoes</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="secondary">Sair</button>
            </form>
        </header>
        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
