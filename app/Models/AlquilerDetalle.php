<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlquilerDetalle extends Model
{
    use HasFactory;

    protected $table = 'AlquilerDetalle';
    protected $primaryKey = 'AlqDetId';
    public $timestamps = false;
    protected $fillable = [
        'AlqDetId',
        'AlqId',
        'HabInmId',
        'AlqDetMonto',
        'AlqDetEstado',
        'AlqDetFechaCreacion',
    ];

    protected $casts = [
        'AlqDetFechaCreacion' => 'datetime',
        'AlqDetMonto' => 'float',
        'AlqDetEstado' => 'boolean',
    ];

    public function alquiler(): BelongsTo
    {
        return $this->belongsTo(Alquiler::class, 'AlqId', 'AlqId');
    }

    public function habitacion_inmueble(): BelongsTo
    {
        return $this->belongsTo(HabitacionInmueble::class, 'HabInmId', 'HabInmId');
    }
}
