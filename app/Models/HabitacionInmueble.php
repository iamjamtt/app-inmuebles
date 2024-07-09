<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HabitacionInmueble extends Model
{
    use HasFactory;

    protected $table = 'HabitacionInmueble';
    protected $primaryKey = 'HabInmId';
    public $timestamps = false;
    protected $fillable = [
        'HabInmId',
        'PisInmId',
        'HabInmNombre',
        'HabInmPrecio',
        'HabInmEstado',
        'HabInmOcupado',
        'HabInmFechaCreacion',
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

    public function alquileres(): HasMany
    {
        return $this->hasMany(AlquilerDetalle::class, 'HabInmId', 'HabInmId');
    }
}
