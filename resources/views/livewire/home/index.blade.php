<?php

use Livewire\Volt\Component;
use App\Models\Usuario;
use App\Models\Inmueble;

new class extends Component {
    public Usuario $usuario;

    public function mount(): void
    {
        $this->usuario = getAuthUsuario();
    }

    public function with(): array
    {
        if ($this->usuario->rol->RolNombre == 'Administrador') {
            $usuarios = Usuario::all();
            $usuarios_count = $usuarios->count();
            $usuarios_de_baja = $usuarios->where('UsuEstado', false)->count();
            $usuarios_de_alta = $usuarios->where('UsuEstado', true)->count();
            $inmuebles_count = Inmueble::all()->count();
            return [
                'usuarios_count' => $usuarios_count,
                'usuarios_de_baja' => $usuarios_de_baja,
                'usuarios_de_alta' => $usuarios_de_alta,
                'inmuebles_count' => $inmuebles_count,
            ];
        } else {
            return [];
        }
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Inicio" subtitle="Bienvenido a la aplicación de inmuebles" separator progress-indicator>
    </x-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- STAT -->
        @if ($usuario->rol->RolNombre == 'Administrador')
            <x-stat title="Usuarios" value="{{ $usuarios_count }}" icon="o-user-group" shadow />
            <x-stat title="Usuarios Dados de Baja" value="{{ $usuarios_de_baja }}" icon="o-user-minus" color="text-error" />
            <x-stat title="Usuarios Dados de Alta" value="{{ $usuarios_de_alta }}" icon="o-user-plus" color="text-teal-600" />
            <x-stat title="Inmuebles" value="{{ $inmuebles_count }}" icon="o-home-modern" color="text-teal-600" />
        @else

        @endif
    </div>
</div>
