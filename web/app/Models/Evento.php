<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public const STATUS_ABERTO = 'aberto';
    public const STATUS_ENCERRADO = 'encerrado';
    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'descricao',
        'data',
        'horario',
        'local',
        'quantidade_vagas',
        'vagas_disponiveis',
        'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(Participante::class, 'inscricoes')
            ->withPivot(['status', 'data_inscricao'])
            ->withTimestamps();
    }
}
