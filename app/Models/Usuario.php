<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'Usuario';
    protected $primaryKey = 'UsuId';
    public $timestamps = false;
    protected $fillable = [
        'UsuId',
        'UsuUsername',
        'UsuContrasena',
        'RolId',
        'PerId',
        'UsuEstado',
        'UsuFechaDadoAlta',
        'UsuFechaDadoBaja',
        'UsuFechaCreacion',
    ];

    protected $casts = [
        'UsuFechaDadoAlta' => 'datetime',
        'UsuFechaDadoBaja' => 'datetime',
        'UsuFechaCreacion' => 'datetime',
        'UsuEstado' => 'boolean',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'RolId', 'RolId');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'PerId', 'PerId');
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereAny([
            'UsuUsername',
        ], 'LIKE', '%' . $search . '%');
    }

    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->UsuUsername) . '&color=f4f4f5&background=3f3f46&bold=true';
    }
}
