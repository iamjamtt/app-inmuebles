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

    // Modal Alerta
    public bool $modalAlerta = false;
    public string $titleModalAlerta = '';
    public string $subtitleModalAlerta = 'Click en confirmar para cambiar el estado del inmueble.';
    public string $buttonModalAlerta = '';
    public string $actionModalAlerta = '';

    public function alertaStatus(Inmueble $inmueble): void
    {
        $this->inmuebleId = $inmueble->InmId;
        if ($inmueble->InmEstado && $inmueble->InmFechaDadoAlta) {
            $this->titleModalAlerta = '¿Estas seguro de dar de baja este inmueble?';
            $this->subtitleModalAlerta = 'Click en confirmar para dar de baja el inmueble.';
        } elseif ($inmueble->InmEstado == false && $inmueble->InmFechaDadoBaja) {
            $this->titleModalAlerta = '¿Estas seguro de dar de alta este inmueble?';
            $this->subtitleModalAlerta = 'Click en confirmar para dar de alta el inmueble.';
        } elseif ($inmueble->InmEstado == false && !$inmueble->InmFechaDadoAlta && !$inmueble->InmFechaDadoBaja) {
            $this->titleModalAlerta = '¿Estas seguro de publicar este inmueble?';
            $this->subtitleModalAlerta = 'Click en confirmar para publicar el inmueble.';
        }
        $this->buttonModalAlerta = 'Confirmar';
        $this->actionModalAlerta = 'changeStatus';
        $this->modalAlerta = true;
    }

    public function changeStatus(): void
    {
        $inmueble = Inmueble::find($this->inmuebleId);
        if ($inmueble->InmEstado && $inmueble->InmFechaDadoAlta) {
            $inmueble->InmEstado = false;
            $inmueble->InmFechaDadoBaja = now();
        } elseif ($inmueble->InmEstado == false && $inmueble->InmFechaDadoBaja) {
            $inmueble->InmEstado = true;
            $inmueble->InmFechaDadoAlta = now();
        } elseif ($inmueble->InmEstado == false && !$inmueble->InmFechaDadoAlta && !$inmueble->InmFechaDadoBaja) {
            $inmueble->InmEstado = true;
            $inmueble->InmFechaDadoAlta = now();
        }
        $inmueble->save();
        $this->success('El estado del inmueble fue cambiado correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function headers(): array
    {
        return [
            ['key' => 'InmId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'InmNombre', 'label' => 'Nombre'],
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
            <x-button label="Registrar" icon="o-plus" class="btn-info" link="/inmuebles/create" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card title="Lista de Inmuebles" class="shadow-lg" shadow separator>
        <x-slot:menu>
            <div class="flex items-center space-x-2">
                <span>Mostrar</span>
                <x-select :options="$cantidadTabla" wire:model.live.debounce="cantidadPage" class="select-sm" />
                <span>registros</span>
            </div>
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$inmuebles" :sort-by="$sortBy" with-pagination >
            @scope('cell_InmId', $item)
            {{ $item->InmId }}
            @endscope
            @scope('cell_InmNombre', $item)
            <div class="flex items-center space-x-2">
                <img src="{{ $item->InmFoto ? asset($item->InmFoto) : 'https://via.placeholder.com/150' }}" alt="Foto"
                    class="w-16 h-16 rounded-lg" />
                <div class="flex flex-col">
                    <span class="font-semibold">{{ $item->InmNombre }}</span>
                    <span class="text-xs text-gray-500">
                        {{ Str::limit($item->InmDescripcion, 50) }}
                    </span>
                </div>
            </div>
            @endscope
            @scope('cell_precio', $item)
            <span class="font-semibold">
                @if (calcularPrecioInmueble($item->InmId) == 0)
                    <span class="text-gray-500">
                        Sin Precio
                    </span>
                @else
                    {{ 'S/. ' . calcularPrecioInmueble($item->InmId) }}
                @endif
            </span>
            @endscope
            @scope('cell_TipInmId', $item)
            {{ $item->tipo_inmueble->TipInmNombre }}
            @endscope
            @scope('cell_InmOcupado', $item)
            @if($item->InmOcupado)
                <div class="badge badge-error">
                    Ocupado
                </div>
            @else
                <div class="badge badge-ghost">
                    Desocupado
                </div>
            @endif
            @endscope
            @scope('cell_InmFechaCreacion', $item)
            {{ convertirHoraFecha($item->InmFechaCreacion) }}
            @endscope
            @scope('cell_InmEstado', $item)
            @if($item->InmEstado && $item->InmFechaDadoAlta)
                <div class="badge badge-info" wire:click="alertaStatus({{ $item->InmId }})" style="cursor: pointer;">
                    <x-icon name="o-check" class="w-4 h-4 me-2" />
                    De Alta
                </div>
            @elseif($item->InmEstado == false && $item->InmFechaDadoBaja)
                <div class="badge badge-error" wire:click="alertaStatus({{ $item->InmId }})" style="cursor: pointer;">
                    <x-icon name="o-x-mark" class="w-4 h-4 me-2" />
                    De Baja
                </div>
            @elseif($item->InmEstado == false && !$item->InmFechaDadoAlta && !$item->InmFechaDadoBaja)
                <div class="badge badge-warning" wire:click="alertaStatus({{ $item->InmId }})" style="cursor: pointer;">
                    <x-icon name="o-information-circle" class="w-4 h-4 me-2" />
                    Pendiente
                </div>
            @endif
            @endscope
            @scope('cell_accion', $item)
            <x-dropdown>
                <x-slot:trigger>
                    <x-button icon="o-ellipsis-horizontal" class="btn-sm" />
                </x-slot:trigger>
                <x-menu-item title="Asignar Pisos" icon="o-building-office" link="/inmuebles/{{ $item->InmId }}/pisos" />
                <x-menu-item title="Editar" icon="o-pencil" link="/inmuebles/{{ $item->InmId }}/edit" />
                <x-menu-item title="Eliminar" icon="o-trash" wire:click="alertaDelete({{ $item->InmId }})" />
            </x-dropdown>
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
