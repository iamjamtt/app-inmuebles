<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvidencia extends Model
{
    use HasFactory;

    protected $table = 'TipoEvidencia';
    protected $primaryKey = 'TipEviId';
    public $timestamps = false;
    protected $fillable = [
        'TipEviId',
        'TipEviNombre',
        'TipEviEstado',
    ];

    protected $casts = [
        'TipEviEstado' => 'boolean',
    ];
}
