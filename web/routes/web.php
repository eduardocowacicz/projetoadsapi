<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/eventos', [PageController::class, 'eventos'])->name('eventos.page');
    Route::get('/participantes', [PageController::class, 'participantes'])->name('participantes.page');
    Route::get('/inscricoes', [PageController::class, 'inscricoes'])->name('inscricoes.page');
});
