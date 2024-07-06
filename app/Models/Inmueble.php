<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inmueble extends Model
{
    use HasFactory;

    protected $table = 'Inmueble';
    protected $primaryKey = 'InmId';
    public $timestamps = false;
    protected $fillable = [
        'InmId',
        'TipInmId',
        'InmNombre',
        'InmDescripcion',
        'InmDireccion',
        'InmFoto',
        'InmEstado',
        'InmOcupado',
        'InmFechaDadoAlta',
        'InmFechaDadoBaja',
        'InmFechaCreacion',
    ];

    protected $casts = [
        'InmFechaDadoAlta' => 'datetime',
        'InmFechaDadoBaja' => 'datetime',
        'InmFechaCreacion' => 'datetime',
        'InmEstado' => 'boolean',
        'InmOcupado' => 'boolean',
    ];

    public function tipo_inmueble(): BelongsTo
    {
        return $this->belongsTo(TipoInmueble::class, 'TipInmId', 'TipInmId');
    }

    public function pisos(): HasMany
    {
        return $this->hasMany(PisoInmueble::class, 'InmId', 'InmId');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->whereAny(['InmNombre', 'InmDescripcion', 'InmDireccion'], 'LIKE', '%' . $search . '%');
        }
    }
}
