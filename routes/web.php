<?php

use Livewire\Volt\Volt;

Volt::route('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');

Volt::route('/inicio', 'home.index')
    ->name('home.index');

Volt::route('/usuarios', 'usuario.index')
    ->middleware('auth')
    ->name('usuario.index');

Volt::route('/inmuebles', 'inmueble.index')
    ->middleware('auth')
    ->name('inmueble.index');
//
