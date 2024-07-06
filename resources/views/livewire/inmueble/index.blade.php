<?php

use Mary\Traits\Toast;
use App\Models\Inmueble;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

new #[Title('Inmuebles | App Inmuebles')] class extends Component {
    use Toast, WithPagination;

    #[Url('buscar')]
    public string $search = '';

    public array $sortBy = ['column' => 'InmId', 'direction' => 'asc'];
    public int $cantidadPage = 5;

    public $inmuebleId = null;

    public function headers(): array
    {
        return [
            ['key' => 'InmId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'InmNombre', 'label' => 'Nombre'],
            ['key' => 'InmDireccion', 'label' => 'Dirección'],
            ['key' => 'precio', 'label' => 'Precio', 'sortable' => false],
            ['key' => 'TipInmId', 'label' => 'Tipo Inmueble'],
            ['key' => 'InmOcupado', 'label' => 'Ocupado'],
            ['key' => 'InmFechaCreacion', 'label' => 'F. Creación'],
            ['key' => 'InmEstado', 'label' => 'Estado'],
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
        $inmuebles = Inmueble::query()
            ->orderBy(...array_values($this->sortBy))
            ->search($this->search)
            ->where('UsuId', auth()->id())
            ->paginate($this->cantidadPage);

        return [
            'inmuebles' => $inmuebles,
            'headers' => $this->headers(),
            'cantidadTabla' => $this->cantidadTabla()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Inmuebles" subtitle="Aquí puedes gestionar los inmuebles registrados en el sistema." separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Buscar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>

        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    {{-- <x-card title="Lista de Usuarios" class="shadow-lg" shadow>
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
                <x-button icon="o-pencil" wire:click="edit({{ $usuario->UsuId }})" class="btn-sm text-blue-500" tooltip="Editar" />
                <x-button icon="o-trash" spinner class="btn-sm text-red-500" tooltip="Eliminar" wire:click="alertaDelete({{ $usuario->UsuId }})" />
            </div>
            @endscope
        </x-table>
    </x-card> --}}
</div>
