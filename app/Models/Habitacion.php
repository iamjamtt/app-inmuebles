<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'Habitacion';
    protected $primaryKey = 'HabId';
    public $timestamps = false;
    protected $fillable = [
        'HabId',
        'PisInmId',
        'HabNombre',
        'HabPrecio',
        'HabEstado',
        'HabOcupado',
        'HabFechaCreacion',
    ];

    protected $casts = [
        'HabFechaCreacion' => 'datetime',
        'HabPrecio' => 'float',
        'HabEstado' => 'boolean',
        'HabOcupado' => 'boolean',
    ];

    public function piso_inmueble(): BelongsTo
    {
        return $this->belongsTo(PisoInmueble::class, 'PisInmId', 'PisInmId');
    }
}
