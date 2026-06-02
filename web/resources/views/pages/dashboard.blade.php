@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Dashboard</h1>
        <p>Use as telas para testar a API de eventos, participantes e inscricoes.</p>
        <div class="grid two">
            <a class="link-card" href="{{ route('eventos.page') }}">Ir para Eventos</a>
            <a class="link-card" href="{{ route('participantes.page') }}">Ir para Participantes</a>
            <a class="link-card" href="{{ route('inscricoes.page') }}">Ir para Inscricoes</a>
        </div>
    </div>
@endsection
