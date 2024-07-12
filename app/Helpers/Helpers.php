<?php

use App\Models\HabitacionInmueble;
use App\Models\Usuario;
use App\Models\Inmueble;

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

function subirArchivo($file, $ruta_archivo, $folders, $nombre_archivo = null, $extension = null)
{
    if (file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }

    // Crear directorios para guardar los archivos
    $base_path = 'files/';
    $path = asignarPermisoFolders($base_path, $folders);

    // Nombre del archivo
    $filename = ($nombre_archivo ? $nombre_archivo : time() . uniqid()) . '.' . ($extension ? $extension : $file->getClientOriginalExtension());
    $nombre_db = $path . $filename;

    // Guardar el archivo
    $file->storeAs($path, $filename, 'public');

    // Asignar todos los permisos al archivo
    chmod($nombre_db, 0777);

    return $nombre_db;
}

function asignarPermisoFolders($base_path, $folders)
{
    $path = $base_path;
    foreach ($folders as $folder) {
        $path .= $folder . '/';
        // Asegurar que se creen los directorios con los permisos correctos
        $parent_directory = dirname($path);
        if (!file_exists($parent_directory)) {
            mkdir($parent_directory, 0777, true); // Establecer permisos en el directorio padre
        }
        if (!file_exists($path)) {
            mkdir($path, 0777, true); // 0777 establece todos los permisos para el directorio
            // Cambiar el modo de permisos después de crear los directorios
            chmod($path, 0777);
        }
    }
    return $path;
}

function calcularPrecioInmueble($InmId)
{
    $inmueble = Inmueble::query()
        ->with('pisos')
        ->find($InmId);
    $precio = 0;
    foreach ($inmueble->pisos as $piso) {
        $habitaciones = $piso->habitaciones()
            ->where('HabInmEstado', true)
            ->where('HabInmOcupado', false)
            ->get();
        foreach ($habitaciones as $habitacion) {
            $precio += $habitacion->HabInmPrecio;
        }
    }
    return number_format($precio, 2, '.', ',');
}

function calcularMontoTotalAlquiler($habitaciones)
{
    $monto = 0;
    foreach ($habitaciones as $habitacion) {
        $habitacion = HabitacionInmueble::find($habitacion);
        $monto += $habitacion->HabInmPrecio;
    }
    $monto = number_format($monto, 2, '.', ',');
    return $monto;
}

function calcularMontoMensualAlquiler($habitaciones, $meses)
{
    $monto = 0;
    foreach ($habitaciones as $habitacion) {
        $habitacion = HabitacionInmueble::find($habitacion);
        $monto += $habitacion->HabInmPrecio;
    }
    $monto = $monto / $meses;
    $monto = number_format($monto, 2, '.', ',');
    return $monto;
}

function calcularFechaFinAlquiler($fecha_inicio, $meses)
{
    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' + ' . $meses . ' months'));
    return $fecha_fin;
}

function modificamosEstadoOcupado($HabInmId, $estado)
{
    $habitacion = HabitacionInmueble::find($HabInmId);
    $habitacion->HabInmOcupado = $estado;
    $habitacion->save();

    // Verificar si todas las habitaciones del piso están ocupadas
    $piso = $habitacion->piso_inmueble;
    $habitaciones = $piso->habitaciones()
        ->where('HabInmEstado', true)
        ->get();
    $habitaciones_ocupadas = $habitaciones->where('HabInmOcupado', true)->count();
    $habitaciones_disponibles = $habitaciones->where('HabInmOcupado', false)->count();
    if ($habitaciones_ocupadas == $habitaciones->count()) {
        $piso->PisInmOcupado = true;
    } else {
        $piso->PisInmOcupado = false;
    }
    $piso->save();

    // Verificar si todos los pisos del inmueble están ocupados
    $inmueble = $piso->inmueble;
    $pisos = $inmueble->pisos()
        ->where('PisInmEstado', true)
        ->get();
    $pisos_ocupados = $pisos->where('PisInmOcupado', true)->count();
    $pisos_disponibles = $pisos->where('PisInmOcupado', false)->count();
    if ($pisos_ocupados == $pisos->count()) {
        $inmueble->InmOcupado = true;
    } else {
        $inmueble->InmOcupado = false;
    }
    $inmueble->save();
}

//
