<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    public const STATUS_ATIVA = 'ativa';
    public const STATUS_CANCELADA = 'cancelada';

    protected $table = 'inscricoes';

    protected $fillable = [
        'evento_id',
        'participante_id',
        'status',
        'data_inscricao',
    ];

    protected $casts = [
        'data_inscricao' => 'datetime',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class);
    }
}
