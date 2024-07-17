<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alquiler extends Model
{
    use HasFactory;

    protected $table = 'Alquiler';
    protected $primaryKey = 'AlqId';
    public $timestamps = false;
    protected $fillable = [
        'AlqId',
        'ArrendadorId',
        'ClienteId',
        'AlqNombre',
        'AlqMontoTotal',
        'AlqMontoMensual',
        'AlqMontoPenalidad',
        'AlqCantidadMeses',
        'AlqFechaInicio',
        'AlqFechaFin',
        'AlqEstado',
        'AlqFinalizado',
        'AlqTienePenalidad',
        'AlqObservacionPenalidad',
        'AlqFechaCreacion',
    ];

    protected $casts = [
        'AlqFechaInicio' => 'datetime',
        'AlqFechaFin' => 'datetime',
        'AlqFechaCreacion' => 'datetime',
        'AlqMontoTotal' => 'float',
        'AlqMontoMensual' => 'float',
        'AlqMontoPenalidad' => 'float',
        'AlqEstado' => 'boolean',
        'AlqFinalizado' => 'boolean',
        'AlqTienePenalidad' => 'boolean',
    ];

    public function arrendador(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'ArrendadorId', 'PerId');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'ClienteId', 'PerId');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AlquilerDetalle::class, 'AlqId', 'AlqId');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoMensualidad::class, 'AlqId', 'AlqId');
    }
}
