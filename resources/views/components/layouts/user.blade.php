<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="lofi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('inmueble-favicon.webp') }}" type="image/x-icon">

    {{-- Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    {{--  Currency  --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    <x-nav with-nav full-width>
        <x-slot:brand>
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            <x-app-inmueble />
        </x-slot:brand>

        @php
        if (auth()->check()) {
            $usuario = App\Models\Usuario::find(auth()->id());
            $rol = $usuario->rol->RolNombre;
        } else {
            $usuario = null;
            $rol = null;
        }
        @endphp

        <x-slot:actions>
            @if ($rol && $rol != 'Cliente')
                <x-button label="Inicio" icon="o-arrow-top-right-on-square" link="/inicio" class="btn-success" responsive />
            @elseif ($rol && $rol == 'Cliente')
                <x-button label="Inicio" icon="o-home" link="/" class="hidden btn-ghost lg:flex" responsive />
            @else
                <x-button label="Inicio" icon="o-home" link="/" class="hidden btn-ghost lg:flex" responsive />
                <x-button label="Ingresar" icon="o-arrow-top-right-on-square" link="/login" class="btn-success" responsive />
                <x-button label="Registrate" icon="o-user-plus" link="/" class="btn-outline" responsive />
            @endif
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        <x-slot:sidebar drawer="main-drawer" collapsible class="lg:hidden bg-base-200">
            <livewire:components.sidebar.menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
