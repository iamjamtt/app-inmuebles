<?php

use Livewire\Volt\Component;

new class extends Component {

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function with(): array
    {
        $usuario = getAuthUsuario();
        return [
            'usuario' => $usuario,
        ];
    }
}; ?>

<x-menu activate-by-route>

    @if ($usuario)
        <x-menu-separator />

        <x-list-item :item="$usuario" sub-value="rol.RolNombre" no-separator no-hover class="-mx-2 !-my-2 rounded">
            <x-slot:value>
                <span class="font-semibold">{{ '@'.$usuario->UsuUsername }}</span>
            </x-slot:value>
            <x-slot:icon>
                <x-avatar :src="$usuario->avatar" class="w-8 h-8" />
            </x-slot:icon>
            <x-slot:actions>
                <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="Cerrar Sesión" no-wire-navigate wire:click="logout" />
            </x-slot:actions>
        </x-list-item>

        <x-menu-separator />

        @if ($usuario->rol->RolNombre == 'Administrador')
        <x-menu-item title="Inicio" icon="o-home" link="/inicio" />
        <x-menu-item title="Usuarios" icon="o-users" link="/usuarios" />
        <x-menu-item title="Reportes" icon="o-chart-bar" link="/reportes" />
        @elseif ($usuario->rol->RolNombre == 'Arrendador')
        <x-menu-item title="Inicio" icon="o-home" link="/inicio" />
        <x-menu-item title="Inmuebles" icon="o-home-modern" link="/inmuebles" />
        @endif
        @if ($usuario->rol->RolNombre == 'Cliente')
        <x-menu-item title="Inicio" icon="o-home" link="/" />
        @endif
        {{-- <x-menu-sub title="Settings" icon="o-cog-6-tooth">
            <x-menu-item title="Wifi" icon="o-wifi" link="####" />
            <x-menu-item title="Archives" icon="o-archive-box" link="####" />
        </x-menu-sub> --}}
    @else
    <x-menu-item title="Inicio" icon="o-home" link="/" />
    <x-menu-item title="Ingresar" icon="o-arrow-top-right-on-square" link="/login" />
    <x-menu-item title="Registrate" icon="o-user-plus" link="/register" />
    @endif

</x-menu>
