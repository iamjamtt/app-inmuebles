<?php

use Livewire\Volt\Volt;

Volt::route('/', 'home.usuario')
    ->name('home.usuario');

Volt::route('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');

Volt::route('/registro', 'auth.registro')
    ->middleware('guest')
    ->name('registro');

Volt::route('/perfil/{UsuId}', 'perfil.index')
    ->middleware('auth')
    ->name('perfil.index');

Volt::route('/inicio', 'home.index')
    ->name('home.index');

Volt::route('/usuarios', 'usuario.index')
    ->middleware('auth')
    ->name('usuario.index');

Volt::route('/inmuebles', 'inmueble.index')
    ->middleware('auth')
    ->name('inmueble.index');

Volt::route('/inmuebles/create', 'inmueble.form-inmueble')
    ->middleware('auth')
    ->name('inmueble.create');

Volt::route('/inmuebles/{InmId}/edit', 'inmueble.form-inmueble')
    ->middleware('auth')
    ->name('inmueble.edit');

Volt::route('/inmuebles/{InmId}/pisos', 'inmueble.asignar-piso')
    ->middleware('auth')
    ->name('inmueble.asignar-piso');
//
