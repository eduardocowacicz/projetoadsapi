@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>Dashboard</h1>
        <p>Use as telas para gerenciar eventos, manter o cadastro de pessoas e realizar inscricoes em eventos.</p>
        <div class="grid two">
            <a class="link-card" href="{{ route('eventos.page') }}">Ir para Eventos</a>
            <a class="link-card" href="{{ route('participantes.page') }}">Ir para Cadastro de Participantes</a>
            <a class="link-card" href="{{ route('inscricoes.page') }}">Ir para Inscricoes</a>
        </div>
    </div>
@endsection
