<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInmueble extends Model
{
    use HasFactory;

    protected $table = 'TipoInmueble';
    protected $primaryKey = 'TipInmId';
    public $timestamps = false;
    protected $fillable = [
        'TipInmId',
        'TipInmNombre',
        'TipInmEstado',
    ];

    protected $casts = [
        'TipInmEstado' => 'boolean',
    ];
}
