<?php

use Mary\Traits\Toast;
use App\Models\Inmueble;
use App\Models\TipoInmueble;
use App\Models\PisoInmueble;
use App\Models\HabitacionInmueble;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

new #[Title('Asignar Pisos a los Inmuebles | App Inmuebles')] class extends Component {
    use Toast;

    public $title_component = '';
    public $subtitle_component = '';

    public Inmueble $inmueble;

    public PisoInmueble $pisoInmueble;

    public HabitacionInmueble $habitacionInmueble;
    public int $cantidad_habitaciones = 0;

    public bool $mostrarHabitaciones = false;

    // Modal Alerta
    public bool $modalAlerta = false;
    public string $titleModalAlerta = '';
    public string $subtitleModalAlerta = 'Click en confirmar para cambiar el estado del piso.';
    public string $buttonModalAlerta = '';
    public string $actionModalAlerta = '';

    // Modal Habitacion
    public bool $modalHabitacion = false;
    public string $titleModal = '';
    public string $subtitleModal = '';
    public string $buttonModal = '';
    public string $actionModal = '';

    public string $nombre_habitacion = '';
    public float $precio_habitacion = 0;

    public function mount($InmId): void
    {
        $inmueble = Inmueble::find($InmId);
        if (!$inmueble) {
            abort(404, 'Inmueble no encontrado.');
        }
        $this->inmueble = $inmueble;
        $this->title_component = 'Asignar Pisos a Inmueble "' . $this->inmueble->InmNombre . '"';
        $this->subtitle_component = 'Aquí puedes asignar pisos al inmueble.';
        $this->modo = 'editar';
        $this->tipoInmueble = $this->inmueble->TipInmId;
        $this->nombre = $this->inmueble->InmNombre;
        $this->direccion = $this->inmueble->InmDireccion;
        $this->descripcion = $this->inmueble->InmDescripcion;
    }

    public function crear_piso(): void
    {
        $this->pisoInmueble = new PisoInmueble();
        $this->pisoInmueble->InmId = $this->inmueble->InmId;
        if ($this->inmueble->pisos()->count() > 0) {
            $this->pisoInmueble->PisInmNumeroPiso = $this->inmueble->pisos->last()->PisInmNumeroPiso + 1;
        } else {
            $this->pisoInmueble->PisInmNumeroPiso = 1;
        }
        $this->pisoInmueble->PisInmEstado = true;
        $this->pisoInmueble->PisInmFechaCreacion = now();
        $this->pisoInmueble->save();

        $this->inmueble->load('pisos');

        // creamos 5 habitaciones por defecto
        for ($i = 1; $i <= 5; $i++) {
            $habitacion = new HabitacionInmueble();
            $habitacion->PisInmId = $this->pisoInmueble->PisInmId;
            $habitacion->HabInmNombre = 'Habitacion ' . $i;
            $habitacion->HabInmPrecio = 0;
            $habitacion->HabInmFechaCreacion = now();
            $habitacion->save();
        }

        $this->success('Piso creado correctamente.', position: 'toast-top toast-center');
    }

    public function crear_habitacion(): void
    {
        $this->habitacionInmueble = new HabitacionInmueble();
        $this->habitacionInmueble->PisInmId = $this->pisoInmueble->PisInmId;
        if ($this->pisoInmueble->habitaciones()->count() > 0) {
            $nombre = $this->pisoInmueble->habitaciones->last()->HabInmNombre;
            $nombre = explode(' ', $nombre);
            $nombre = end($nombre);
            $this->habitacionInmueble->HabInmNombre = 'Habitacion ' . ($nombre + 1);
        } else {
            $this->habitacionInmueble->HabInmNombre = 'Habitacion 1';
        }
        $this->habitacionInmueble->HabInmPrecio = 0;
        $this->habitacionInmueble->HabInmEstado = false;
        $this->habitacionInmueble->HabInmFechaCreacion = now();
        $this->habitacionInmueble->save();

        $this->inmueble->load('pisos');
        $this->pisoInmueble->load('habitaciones');
        $this->cantidad_habitaciones = $this->pisoInmueble->habitaciones()->count();
        $this->mostrarHabitaciones = true;

        $this->success('Habitación creada correctamente.', position: 'toast-top toast-center');
    }

    public function abrir_piso(PisoInmueble $piso_inmueble): void
    {
        $this->mostrarHabitaciones = true;
        $this->pisoInmueble = $piso_inmueble;
        $this->cantidad_habitaciones = $this->pisoInmueble->habitaciones()->count();
    }

    public function alertaStatus($id, $tipo): void
    {
        if ($tipo === 'piso') {
            $piso_inmueble = PisoInmueble::find($id);
            $this->pisoInmueble = $piso_inmueble;
            $this->titleModalAlerta = '¿Estas seguro de cambiar el estado de este piso?';
            $this->subtitleModalAlerta = 'Click en confirmar para cambiar el estado del piso.';
            $this->buttonModalAlerta = 'Confirmar';
            $this->actionModalAlerta = 'change_status_piso';
            $this->modalAlerta = true;
        } elseif ($tipo === 'habitacion') {
            $habitacion_inmueble = HabitacionInmueble::find($id);
            // verificamos si la habitacion esta ocupada
            if ($habitacion_inmueble->HabInmOcupado) {
                $this->error('No se puede cambiar el estado de una habitación ocupada.', position: 'toast-top toast-center');
                return;
            }
            
            $this->habitacionInmueble = $habitacion_inmueble;
            $this->titleModalAlerta = '¿Estas seguro de cambiar el estado de esta habitación?';
            $this->subtitleModalAlerta = 'Click en confirmar para cambiar el estado de la habitación.';
            $this->buttonModalAlerta = 'Confirmar';
            $this->actionModalAlerta = 'change_status_habitacion';
            $this->modalAlerta = true;
        }
    }

    public function change_status_piso(): void
    {
        $this->pisoInmueble->PisInmEstado = !$this->pisoInmueble->PisInmEstado;
        $this->pisoInmueble->save();

        $this->inmueble->load('pisos');
        $this->mostrarHabitaciones = false;

        $this->success('Estado del piso cambiado correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function change_status_habitacion(): void
    {
        $this->habitacionInmueble->HabInmEstado = !$this->habitacionInmueble->HabInmEstado;
        $this->habitacionInmueble->save();

        // verificamos si tiene al menos una habitacion activa para mostrar las habitaciones
        $this->pisoInmueble->load('habitaciones');
        $this->cantidad_habitaciones = $this->pisoInmueble->habitaciones()->count();

        modificamosEstadoOcupado($this->habitacionInmueble->HabInmId, false);

        $this->inmueble->load('pisos');
        $this->mostrarHabitaciones = true;

        $this->success('Estado de la habitación cambiado correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function alertaDelete($id, $tipo): void
    {
        if ($tipo === 'piso') {
            $piso_inmueble = PisoInmueble::find($id);
            $this->pisoInmueble = $piso_inmueble;
            $this->titleModalAlerta = '¿Estas seguro de eliminar este piso?';
            $this->subtitleModalAlerta = 'Click en confirmar para eliminar el piso.';
            $this->buttonModalAlerta = 'Confirmar';
            $this->actionModalAlerta = 'delete_piso';
            $this->modalAlerta = true;
        } elseif ($tipo === 'habitacion')
        {
            $habitacion_inmueble = HabitacionInmueble::find($id);
            $this->habitacionInmueble = $habitacion_inmueble;
            $this->titleModalAlerta = '¿Estas seguro de eliminar esta habitación?';
            $this->subtitleModalAlerta = 'Click en confirmar para eliminar la habitación.';
            $this->buttonModalAlerta = 'Confirmar';
            $this->actionModalAlerta = 'delete_habitacion';
            $this->modalAlerta = true;
        }

    }

    public function delete_piso(): void
    {
        // Validar si el piso esta ocupado
        if ($this->pisoInmueble->PisInmOcupado) {
            $this->error('No puedes eliminar un piso ocupado.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        // validar si el piso tiene habitaciones ocupadas
        if ($this->pisoInmueble->habitaciones()->where('HabInmOcupado', true)->count() > 0) {
            $this->error('No puedes eliminar un piso con habitaciones ocupadas.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        // validar si el piso tiene habitaciones en alquiler
        $habitaciones = $this->pisoInmueble->habitaciones();
        $tiene_alquileres = false;
        foreach ($habitaciones as $habitacion) {
            if ($habitacion->alquileres()->count() > 0) {
                $tiene_alquileres = true;
                break;
            }
        }
        if ($tiene_alquileres) {
            $this->error('No puedes eliminar un piso con habitaciones en alquiler o historial de alquileres, solo cambiarlas de estas.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        // Eliminar las habitaciones
        $this->pisoInmueble->habitaciones()->delete();
        // Eliminar el piso
        $this->pisoInmueble->delete();

        $this->inmueble->load('pisos');
        $this->mostrarHabitaciones = false;

        $this->success('Piso eliminado correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function delete_habitacion(): void
    {
        // Validar si la habitacion esta ocupada
        if ($this->habitacionInmueble->HabInmOcupado) {
            $this->error('No puedes eliminar una habitación ocupada.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        // validar si la habitacion tiene alquileres
        if ($this->habitacionInmueble->alquileres()->count() > 0) {
            $this->error('No puedes eliminar una habitación con alquileres.', position: 'toast-top toast-center');
            $this->modalAlerta = false;
            return;
        }
        // Eliminar la habitacion
        $this->habitacionInmueble->delete();

        $this->inmueble->load('pisos');
        $this->mostrarHabitaciones = true;

        $this->cantidad_habitaciones = $this->pisoInmueble->habitaciones()->count();

        $this->success('Habitación eliminada correctamente.', position: 'toast-top toast-center');
        $this->modalAlerta = false;
    }

    public function reset_modal_habitacion(): void
    {
        $this->nombre_habitacion = '';
        $this->precio_habitacion = 0;
    }

    public function editar_habitacion(HabitacionInmueble $habitacion_inmueble): void
    {
        $this->habitacionInmueble = $habitacion_inmueble;
        $this->titleModal = 'Editar Habitación';
        $this->subtitleModal = 'Aquí puedes editar la habitación.';
        $this->buttonModal = 'Actualizar';
        $this->actionModal = 'update_habitacion';
        $this->modalHabitacion = true;

        $this->nombre_habitacion = $this->habitacionInmueble->HabInmNombre;
        $this->precio_habitacion = $this->habitacionInmueble->HabInmPrecio;
    }

    public function update_habitacion(): void
    {
        $this->validate([
            'nombre_habitacion' => 'required|string|max:255',
            'precio_habitacion' => 'required|numeric|min:0',
        ]);

        $this->habitacionInmueble->HabInmNombre = $this->nombre_habitacion;
        $this->habitacionInmueble->HabInmPrecio = $this->precio_habitacion;
        $this->habitacionInmueble->save();

        $this->inmueble->load('pisos');
        $this->mostrarHabitaciones = true;

        $this->success('Habitación actualizada correctamente.', position: 'toast-top toast-center');
        $this->modalHabitacion = false;
    }

    public function headers_pisos(): array
    {
        return [
            ['key' => 'PisInmId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'PisInmNumeroPiso', 'label' => 'Nombre'],
            ['key' => 'PisInmOcupado', 'label' => 'Ocupado'],
            ['key' => 'PisInmEstado', 'label' => 'Estado'],
            ['key' => 'accion', 'label' => 'Acciones'],
        ];
    }

    public function headers_habitaciones(): array
    {
        return [
            ['key' => 'HabInmId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'HabInmNombre', 'label' => 'Nombre'],
            ['key' => 'HabInmPrecio', 'label' => 'Nombre'],
            ['key' => 'HabInmOcupado', 'label' => 'Ocupado'],
            ['key' => 'HabInmEstado', 'label' => 'Estado'],
            ['key' => 'accion', 'label' => 'Acciones'],
        ];
    }

    public function with(): array
    {
        $tipos_inmuebles = TipoInmueble::query()
            ->where('TipInmEstado', true)
            ->get();

        return [
            'tipos_inmuebles' => $tipos_inmuebles,
            'headers_pisos' => $this->headers_pisos(),
            'headers_habitaciones' => $this->headers_habitaciones()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header :title="$title_component" :subtitle="$subtitle_component" separator>
        <x-slot:actions>
            <x-button label="Regresar" icon="o-arrow-left" link="/inmuebles" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
        <div class="col-span-1">
            <x-card title="Pisos del Inmueble" shadow separator>
                <x-table :headers="$headers_pisos" :rows="$inmueble->pisos" >
                    @scope('cell_PisInmId', $item)
                    {{ $loop->iteration }}
                    @endscope
                    @scope('cell_PisInmNumeroPiso', $item)
                    Piso {{ $item->PisInmNumeroPiso }}
                    @endscope
                    @scope('cell_PisInmOcupado', $item)
                    @if($item->PisInmOcupado)
                        <div class="badge badge-error">
                            Ocupado
                        </div>
                    @else
                        <div class="badge badge-ghost">
                            Desocupado
                        </div>
                    @endif
                    @endscope
                    @scope('cell_PisInmEstado', $item)
                    @if($item->PisInmEstado)
                        <div class="badge badge-info" wire:click="alertaStatus({{ $item->PisInmId }}, 'piso')" style="cursor: pointer;">
                            <x-icon name="o-check" class="w-4 h-4 me-2" />
                            Activo
                        </div>
                    @else
                        <div class="badge badge-error" wire:click="alertaStatus({{ $item->PisInmId }}, 'piso')" style="cursor: pointer;">
                            <x-icon name="o-x-mark" class="w-4 h-4 me-2" />
                            Inactivo
                        </div>
                    @endif
                    @endscope
                    @scope('cell_accion', $item)
                    <x-dropdown>
                        <x-slot:trigger>
                            <x-button icon="o-ellipsis-horizontal" class="btn-sm" />
                        </x-slot:trigger>
                        <x-menu-item title="Ver Habitaciones" icon="o-building-office" wire:click="abrir_piso({{ $item->PisInmId }})" />
                    </x-dropdown>
                    <x-button icon="o-trash" spinner class="text-error btn-sm" tooltip="Eliminar" wire:click="alertaDelete({{ $item->PisInmId }}, 'piso')" />
                    @endscope
                </x-table>
                <x-slot:menu>
                    <x-button icon="o-plus" class="btn-outline btn-sm" wire:click="crear_piso" />
                </x-slot:menu>
            </x-card>
        </div>
        @if ($mostrarHabitaciones)
        <div class="col-span-1">
            <x-card title="Habitaciones del Piso {{ $pisoInmueble?->PisInmNumeroPiso }}" shadow separator>
                <x-table :headers="$headers_habitaciones" :rows="$pisoInmueble?->habitaciones" >
                    @scope('cell_HabInmId', $item)
                    {{ $loop->iteration }}
                    @endscope
                    @scope('cell_HabInmNombre', $item)
                    {{ $item->HabInmNombre }}
                    @endscope
                    @scope('cell_HabInmPrecio', $item)
                    S/. {{ $item->HabInmPrecio }}
                    @endscope
                    @scope('cell_HabInmOcupado', $item)
                    @if($item->HabInmOcupado)
                        <div class="badge badge-error">
                            Ocupado
                        </div>
                    @else
                        <div class="badge badge-ghost">
                            Desocupado
                        </div>
                    @endif
                    @endscope
                    @scope('cell_HabInmEstado', $item)
                    @if($item->HabInmEstado)
                        <div class="badge badge-info" wire:click="alertaStatus({{ $item->HabInmId }}, 'habitacion')" style="cursor: pointer;">
                            <x-icon name="o-check" class="w-4 h-4 me-2" />
                            Activo
                        </div>
                    @else
                        <div class="badge badge-error" wire:click="alertaStatus({{ $item->HabInmId }}, 'habitacion')" style="cursor: pointer;">
                            <x-icon name="o-x-mark" class="w-4 h-4 me-2" />
                            Inactivo
                        </div>
                    @endif
                    @endscope
                    @scope('cell_accion', $item)
                    <x-dropdown>
                        <x-slot:trigger>
                            <x-button icon="o-ellipsis-horizontal" class="btn-sm" />
                        </x-slot:trigger>
                        <x-menu-item title="Editar" icon="o-pencil" wire:click="editar_habitacion({{ $item->HabInmId }}, 'habitacion')" />
                        <x-menu-item title="Eliminar" icon="o-trash" wire:click="alertaDelete({{ $item->HabInmId }}, 'habitacion')" />
                    </x-dropdown>
                    @endscope
                </x-table>
                @if ($cantidad_habitaciones !== 5)
                <x-slot:menu>
                    <x-button icon="o-plus" class="btn-outline" wire:click="crear_habitacion" />
                </x-slot:menu>
                @endif
            </x-card>
        </div>
        @endif
    </div>


    <!-- MODALS -->
    <x-modal wire:model="modalHabitacion" title="{{ $titleModal }}" subtitle="{{ $subtitleModal }}" separator>
        <x-form>
            <x-input label="Nombre" required wire:model="nombre_habitacion" placeholder="Ingrese el nombre de la habitación" />
            <x-input label="Precio" required wire:model="precio_habitacion" money locale="es-PE" />
        </x-form>

        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalHabitacion = false" wire:click="reset_modal_habitacion" />
            <x-button label="{{ $buttonModal }}" class="btn-primary" wire:click="{{ $actionModal }}" />
        </x-slot:actions>
    </x-modal>

    <!-- MODALS -->
    <x-modal wire:model="modalAlerta" title="{{ $titleModalAlerta }}" subtitle="{{ $subtitleModalAlerta }}">
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalAlerta = false" />
            <x-button label="{{ $buttonModalAlerta }}" class="btn-success" wire:click="{{ $actionModalAlerta }}" />
        </x-slot:actions>
    </x-modal>
</div>
