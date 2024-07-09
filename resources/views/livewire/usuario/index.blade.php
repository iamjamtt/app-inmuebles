<?php

use Mary\Traits\Toast;
use App\Models\Usuario;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;

new #[Title('Usuarios | App Inmuebles')] class extends Component {
    use Toast, WithPagination;

    #[Url('buscar')]
    public string $search = '';

    public array $sortBy = ['column' => 'UsuId', 'direction' => 'asc'];
    public int $cantidadPage = 5;

    #[Url('filEst')]
    public string $estadoFiltro = '';

    public $usuarioId = null;
    public bool $modalAlerta = false;
    public string $titleModalAlerta = '';
    public string $subtitleModalAlerta = 'Click en confirmar para cambiar el estado del usuario.';
    public string $buttonModalAlerta = '';
    public string $actionModalAlerta = '';

    public function mount(): void
    {
        $usuario = getAuthUsuario();
        if ($usuario->rol->RolNombre !== 'Administrador') {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
    }

    public function alertaDelete(Usuario $usuario): void
    {
        $this->usuarioId = $usuario->UsuId;
        $this->titleModalAlerta = '¿Estas seguro de eliminar este usuario?';
        $this->subtitleModalAlerta = 'Click en confirmar para eliminar el usuario.';
        $this->buttonModalAlerta = 'Eliminar Usuario';
        $this->actionModalAlerta = 'delete';
        $this->modalAlerta = true;
    }

    public function delete(): void
    {
        $usuario = Usuario::find($this->usuarioId);
        if ($usuario->rol->RolNombre === 'Administrador') {
            $this->error('No puedes eliminar un usuario administrador.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        $usuario->delete();
        $this->success('Usuario eliminado correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function alertaStatus(Usuario $usuario): void
    {
        $this->usuarioId = $usuario->UsuId;
        if ($usuario->UsuEstado) {
            $this->titleModalAlerta = '¿Estas seguro de dar de baja a este usuario?';
        } else {
            $this->titleModalAlerta = '¿Estas seguro de dar de alta a este usuario?';
        }
        $this->subtitleModalAlerta = 'Click en confirmar para cambiar el estado del usuario.';
        $this->buttonModalAlerta = 'Confirmar';
        $this->actionModalAlerta = 'changeStatus';
        $this->modalAlerta = true;
    }

    public function changeStatus(): void
    {
        $usuario = Usuario::find($this->usuarioId);
        if ($usuario->rol->RolNombre === 'Administrador') {
            $this->error('No se puede cambiar el estado de un usuario administrador.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        $usuario->UsuEstado = !$usuario->UsuEstado;
        if ($usuario->UsuEstado) {
            $usuario->UsuFechaDadoAlta = now();
        } else {
            $usuario->UsuFechaDadoBaja = now();
        }
        $usuario->save();
        $this->success('El estado del usuario fue cambiado correctamente.', position: 'toast-top toast-center');
        $this->modalEstado = false;
    }

    public function headers(): array
    {
        return [
            ['key' => 'UsuId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'UsuUsername', 'label' => 'Username'],
            ['key' => 'RolId', 'label' => 'Rol'],
            ['key' => 'UsuFechaCreacion', 'label' => 'F. Creación'],
            ['key' => 'UsuEstado', 'label' => 'Estado'],
            ['key' => 'accion', 'label' => 'Acciones', 'class' => 'w-32', 'sortable' => false],
        ];
    }

    public function cantidadTabla(): array
    {
        return [
            ['id' => 10, 'name' => '5'],
            ['id' => 10, 'name' => '10'],
            ['id' => 20, 'name' => '20'],
            ['id' => 50, 'name' => '50'],
            ['id' => 100, 'name' => '100'],
        ];
    }

    public function with(): array
    {
        $usuarios = Usuario::query()
            ->orderBy(...array_values($this->sortBy))
            ->search($this->search)
            ->where(function ($query) {
                if ($this->estadoFiltro === '1') {
                    $query->where('UsuEstado', true);
                } elseif ($this->estadoFiltro === '0') {
                    $query->where('UsuEstado', false);
                }
            })
            ->paginate($this->cantidadPage);

        $estados = [
            ['id' => null, 'name' => 'Mostrar Todos'],
            ['id' => 1, 'name' => 'Usuarios Dado De Alta'],
            ['id' => 0, 'name' => 'Usuarios Dado De Baja'],
        ];
        return [
            'usuarios' => $usuarios,
            'headers' => $this->headers(),
            'cantidadTabla' => $this->cantidadTabla(),
            'estados' => $estados,
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Usuarios" subtitle="Aquí puedes administrar los usuarios registrados en el sistema de inmuebles." separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Buscar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-select :options="$estados" wire:model.live="estadoFiltro" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card title="Lista de Usuarios" class="shadow-lg" shadow>
        <x-slot:menu>
            <div class="flex items-center space-x-2">
                <span>Mostrar</span>
                <x-select :options="$cantidadTabla" wire:model.live.debounce="cantidadPage" class="select-sm" />
                <span>registros</span>
            </div>
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$usuarios" :sort-by="$sortBy" with-pagination >
            @scope('cell_UsuId', $usuario)
            {{ $usuario->UsuId }}
            @endscope
            @scope('cell_UsuUsername', $usuario)
            {{ '@'.$usuario->UsuUsername }}
            @endscope
            @scope('cell_RolId', $usuario)
            <div class="badge badge-{{ colorRol($usuario->rol->RolNombre) }}">
                {{ $usuario->rol->RolNombre }}
            </div>
            @endscope
            @scope('cell_UsuFechaCreacion', $usuario)
            {{ convertirHoraFecha($usuario->UsuFechaCreacion) }}
            @endscope
            @scope('cell_UsuEstado', $usuario)
            @if($usuario->UsuEstado)
                <div class="badge badge-info" wire:click="alertaStatus({{ $usuario->UsuId }})" style="cursor: pointer;">
                    <x-icon name="o-check" class="w-4 h-4 me-2" />
                    De Alta
                </div>
            @else
                <div class="badge badge-error" wire:click="alertaStatus({{ $usuario->UsuId }})" style="cursor: pointer;">
                    <x-icon name="o-x-mark" class="w-4 h-4 me-2" />
                    De Baja
                </div>
            @endif
            @endscope
            @scope('cell_accion', $usuario)
            <div class="flex space-x-2">
                {{-- <x-button icon="o-pencil" wire:click="edit({{ $usuario->UsuId }})" class="text-blue-500 btn-sm" tooltip="Editar" /> --}}
                <x-button icon="o-trash" spinner class="text-red-500 btn-sm" tooltip="Eliminar" wire:click="alertaDelete({{ $usuario->UsuId }})" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- MODALS -->
    <x-modal wire:model="modalAlerta" title="{{ $titleModalAlerta }}" subtitle="{{ $subtitleModalAlerta }}">
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalAlerta = false" />
            <x-button label="{{ $buttonModalAlerta }}" class="btn-success" wire:click="{{ $actionModalAlerta }}" />
        </x-slot:actions>
    </x-modal>
</div>
