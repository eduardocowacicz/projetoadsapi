<?php

use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\ParticipanteController;
use Illuminate\Support\Facades\Route;

Route::get('/eventos/vagas-disponiveis', [EventoController::class, 'vagasDisponiveis']);
Route::get('/eventos/{id}/participantes', [EventoController::class, 'participantes'])
    ->whereNumber('id');
Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/{id}', [EventoController::class, 'show'])
    ->whereNumber('id');
Route::post('/eventos', [EventoController::class, 'store']);
Route::put('/eventos/{id}', [EventoController::class, 'update'])
    ->whereNumber('id');
Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])
    ->whereNumber('id');

Route::get('/participantes', [ParticipanteController::class, 'index']);
Route::get('/participantes/{id}', [ParticipanteController::class, 'show'])
    ->whereNumber('id');
Route::post('/participantes', [ParticipanteController::class, 'store']);
Route::put('/participantes/{id}', [ParticipanteController::class, 'update'])
    ->whereNumber('id');
Route::delete('/participantes/{id}', [ParticipanteController::class, 'destroy'])
    ->whereNumber('id');

Route::get('/inscricoes', [InscricaoController::class, 'index']);
Route::post('/inscricoes', [InscricaoController::class, 'store']);
Route::delete('/inscricoes/{id}', [InscricaoController::class, 'destroy'])
    ->whereNumber('id');
