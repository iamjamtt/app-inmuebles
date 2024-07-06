<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PagoMensualidad extends Model
{
    use HasFactory;

    protected $table = 'PagoMensualidad';
    protected $primaryKey = 'PagMenId';
    public $timestamps = false;
    protected $fillable = [
        'PagMenId',
        'AlqId',
        'PagMenMontoPago',
        'PagMenMontoPagado',
        'PagMenEstado',
        'PagMenFechaPago',
    ];

    protected $casts = [
        'PagMenFechaPago' => 'datetime',
        'PagMenMontoPago' => 'float',
        'PagMenMontoPagado' => 'float',
        'PagMenEstado' => 'boolean',
    ];

    public function alquiler(): BelongsTo
    {
        return $this->belongsTo(Alquiler::class, 'AlqId', 'AlqId');
    }
}
