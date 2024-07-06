<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PisoInmueble extends Model
{
    use HasFactory;

    protected $table = 'PisoInmueble';
    protected $primaryKey = 'PisInmId';
    public $timestamps = false;
    protected $fillable = [
        'PisInmId',
        'InmId',
        'PisInmNumeroPiso',
        'PisInmEstado',
        'PisInmOcupado',
        'PisInmFechaCreacion',
    ];

    protected $casts = [
        'PisInmFechaCreacion' => 'datetime',
        'PisInmEstado' => 'boolean',
        'PisInmOcupado' => 'boolean',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(Inmueble::class, 'InmId', 'InmId');
    }

    public function habitaciones(): HasMany
    {
        return $this->hasMany(Habitacion::class, 'PisInmId', 'PisInmId');
    }
}
