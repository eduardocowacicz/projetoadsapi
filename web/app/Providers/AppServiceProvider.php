<?php

namespace App\Providers;

use App\Interfaces\EventoRepositoryInterface;
use App\Interfaces\InscricaoRepositoryInterface;
use App\Interfaces\ParticipanteRepositoryInterface;
use App\Repositories\EventoRepository;
use App\Repositories\InscricaoRepository;
use App\Repositories\ParticipanteRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventoRepositoryInterface::class, EventoRepository::class);
        $this->app->bind(ParticipanteRepositoryInterface::class, ParticipanteRepository::class);
        $this->app->bind(InscricaoRepositoryInterface::class, InscricaoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
