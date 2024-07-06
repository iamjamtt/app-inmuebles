<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'Persona';
    protected $primaryKey = 'PerId';
    public $timestamps = false;
    protected $fillable = [
        'PerId',
        'PerDocumentoIdentidad',
        'PerApellidoPaterno',
        'PerApellidoMaterno',
        'PerNombres',
        'PerFechaNacimiento',
        'PerSexo',
        'PerCorreo',
        'PerDireccion',
        'PerEstado',
        'PerFechaCreacion',
        'TipEviId',
        'PerTipoEvidenciaArchivo',
        'PerReferenciaAlquiler',
    ];

    protected $casts = [
        'PerFechaNacimiento' => 'datetime',
        'PerFechaCreacion' => 'datetime',
        'PerEstado' => 'boolean',
    ];

    public function tipo_evidencia(): BelongsTo
    {
        return $this->belongsTo(TipoEvidencia::class, 'TipEviId', 'TipEviId');
    }
}
