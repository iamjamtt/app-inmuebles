<?php

use App\Models\Usuario;

function getUsuario($id): Usuario
{
    $usuario = Usuario::find($id);
    return $usuario;
}

function getAuthUsuario()
{
    return auth()->user();
}

function convertirHoraFecha($fecha): string
{
    return date('h:i A d/m/Y', strtotime($fecha));
}

function convertirFecha($fecha): string
{
    return date('d/m/Y', strtotime($fecha));
}

function colorRol($rol): string
{
    $color = '';
    switch ($rol) {
        case 'Administrador':
            $color = 'primary';
            break;
        case 'Arrendador':
            $color = 'ghost';
            break;
        case 'Cliente':
            $color = 'warning';
            break;
    }
    return $color;
}

//
