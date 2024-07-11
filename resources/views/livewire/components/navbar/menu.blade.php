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
        if (auth()->check()) {
            $usuario = App\Models\Usuario::find(auth()->id());
            $rol = $usuario->rol->RolNombre;
        } else {
            $usuario = null;
            $rol = null;
        }

        return [
            'usuario' => $usuario,
            'rol' => $rol,
        ];
    }
}; ?>

<div class="flex items-center space-x-4">
    @if ($rol && $rol != 'Cliente')
        <x-button label="Inicio" icon="o-arrow-top-right-on-square" link="/inicio" class="btn-success" responsive />
    @elseif ($rol && $rol == 'Cliente')
        <x-button label="Inicio" icon="o-home" link="/" class="hidden btn-ghost lg:flex" responsive />
        <x-dropdown label="Settings" class="btn-ghost" right>
            <x-slot:label>
                <x-avatar :image="$usuario->avatar" />
                {{ '@'.$usuario->UsuUsername }}
            </x-slot:label>
            <x-menu-item title="Inicio" icon="o-home" link="/" />
            <x-menu-separator />
            <x-menu-item title="Cerrar Sesión" icon="o-power" wire:click="logout" />
        </x-dropdown>
    @else
        <x-button label="Inicio" icon="o-home" link="/" class="hidden btn-ghost lg:flex" responsive />
        <x-button label="Ingresar" icon="o-arrow-top-right-on-square" link="/login" class="btn-success" responsive />
        <x-button label="Registrate" icon="o-user-plus" link="/registro" class="btn-outline" responsive />
    @endif
</div>
