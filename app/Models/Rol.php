<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'Rol';
    protected $primaryKey = 'RolId';
    public $timestamps = false;
    protected $fillable = [
        'RolId',
        'RolNombre',
        'RolEstado',
    ];

    protected $casts = [
        'RolEstado' => 'boolean',
    ];
}
