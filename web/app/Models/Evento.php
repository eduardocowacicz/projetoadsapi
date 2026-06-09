<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $titulo
 * @property string|null $descricao
 * @property string $data
 * @property string $horario
 * @property string $local
 * @property int $quantidade_vagas
 * @property int $vagas_disponiveis
 * @property string $status
 */

class Evento extends Model
{
    public const STATUS_ABERTO = 'aberto';
    public const STATUS_ENCERRADO = 'encerrado';
    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'eventos';

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(Participante::class, 'inscricoes')
            ->withPivot(['status', 'data_inscricao'])
            ->withTimestamps();
    }
}
