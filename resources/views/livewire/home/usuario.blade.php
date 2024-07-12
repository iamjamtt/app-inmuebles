<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Inmueble;
use App\Models\Alquiler;
use App\Models\AlquilerDetalle;
use App\Models\HabitacionInmueble;
use App\Models\PagoMensualidad;
use App\Models\TipoInmueble;
use App\Mail\AlquilerMail;
use Livewire\WithPagination;

new #[Title('App Inmuebles')] #[Layout('components.layouts.user')] class extends Component {
    use Toast, WithPagination;

    public $tipoInmuebleFiltro = null;

    public Inmueble $inmueble;

    #[Validate('required|array')]
    public $habitaciones = [];
    #[Validate('required|numeric')]
    public $meses = 1;
    #[Validate('required|date')]
    public $fecha_inicio = null;

    // Modal Inmueble
    public bool $modalInmueble = false;
    public string $titleModal = '';
    public string $subtitleModal = '';
    public string $buttonModal = '';
    public string $actionModal = '';

    public function cargar_inmueble($InmId): void
    {
        $this->reset_modal_inmueble();

        if (auth()->check()) {
            $usuario = auth()->user();
            if ($usuario->rol->RolNombre === 'Cliente') {
                $inmueble = Inmueble::query()
                    ->where('InmId', $InmId)
                    ->with('pisos.habitaciones')
                    ->first();
                $this->inmueble = $inmueble;
                $this->titleModal = 'Inmueble: ' . $inmueble->InmNombre;
                $this->subtitleModal = 'Aquí puedes ver la información del inmueble.';
                $this->buttonModal = 'Alquilar';
                $this->actionModal = 'alquilar_inmueble';
                $this->modalInmueble = true;
            } else {
                $this->error('No tienes permisos para alquilar un inmueble.', position: 'toast-top toast-center');
                return;
            }
        } else {
            $this->error('Debes iniciar sesión para alquilar un inmueble.', position: 'toast-top toast-center');
            return;
        }
    }

    public function reset_modal_inmueble(): void
    {
        $this->reset([
            'habitaciones',
            'meses',
            'fecha_inicio',
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function alquilar_inmueble(): void
    {
        // validar que se haya seleccionado al menos una habitación
        if (count($this->habitaciones) == 0) {
            $this->error('Debes seleccionar al menos una habitación para alquilar el inmueble.', position: 'toast-top toast-center');
        }

        $this->validate();

        // creamos el alquiler
        $alquiler = new Alquiler();
        $alquiler->ArrendadorId = $this->inmueble->usuario->PerId;
        $alquiler->ClienteId = auth()->user()->PerId;
        $alquiler->AlqNombre = 'Alquiler de Inmueble - ' . $this->inmueble->InmNombre;
        $alquiler->AlqMontoTotal = calcularMontoTotalAlquiler($this->habitaciones);
        $alquiler->AlqMontoMensual = calcularMontoMensualAlquiler($this->habitaciones, $this->meses);
        $alquiler->AlqMontoPenalidad = 0;
        $alquiler->AlqCantidadMeses = $this->meses;
        $alquiler->AlqFechaInicio = $this->fecha_inicio;
        $alquiler->AlqFechaFin = calcularFechaFinAlquiler($this->fecha_inicio, $this->meses);
        $alquiler->AlqFechaCreacion = now();
        $alquiler->save();

        // creamos los detalles del alquiler
        foreach ($this->habitaciones as $item) {
            $habitacion = HabitacionInmueble::query()
                ->where('HabInmId', $item)
                ->first();
            $alquiler_detalle = new AlquilerDetalle();
            $alquiler_detalle->AlqId = $alquiler->AlqId;
            $alquiler_detalle->HabInmId = $item;
            $alquiler_detalle->AlqDetMonto = $habitacion->HabInmPrecio;
            $alquiler_detalle->AlqDetFechaCreacion = now();
            $alquiler_detalle->save();
        }

        // creamos los pagos de mensualidad
        for ($i = 0; $i < $this->meses; $i++) {
            $pago = new PagoMensualidad();
            $pago->AlqId = $alquiler->AlqId;
            $pago->PagMenMontoPago = calcularMontoMensualAlquiler($this->habitaciones, $this->meses);
            $pago->PagMenMontoPagado = 0;
            $pago->save();
        }

        // actualizamos el estado de las habitaciones
        foreach ($this->habitaciones as $habitacion) {
            modificamosEstadoOcupado($habitacion, true);
        }

        // mandamos al correo del cliente su alquiler
        $usuario = auth()->user();
        Mail::to($usuario->persona->PerCorreo)->send(new AlquilerMail($usuario->UsuId, $alquiler->AlqId));

        $this->success('El alquiler se realizó correctamente.', position: 'toast-top toast-center');
        $this->reset_modal_inmueble();
        $this->modalInmueble = false;
    }

    public function with(): array
    {
        $inmuebles = Inmueble::query()
            ->where('InmEstado', true)
            ->where('InmOcupado', false)
            ->where(function ($query) {
                if ($this->tipoInmuebleFiltro) {
                    $query->where('TipInmId', $this->tipoInmuebleFiltro);
                }
            })
            ->paginate(6);

        $tipos_inmuebles = TipoInmueble::query()
            ->where('TipInmEstado', true)
            ->get();
        return [
            'inmuebles' => $inmuebles,
            'tipos_inmuebles' => $tipos_inmuebles,
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Inmuebles" subtitle="Aquí puedes encontrar los inmuebles que tenemos disponibles para ti." separator
        progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-select
                :options="$tipos_inmuebles"
                option-value="TipInmId"
                option-label="TipInmNombre"
                placeholder="Mostar todos los tipos de inmuebles"
                wire:model.live="tipoInmuebleFiltro"
            />
        </x-slot:middle>
    </x-header>

    {{-- mostrar card de inmuebles para el usuario --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($inmuebles as $item)
            <x-card title="{{ $item->InmNombre }}" wire:key="{{ $item->InmId }}">
                {{ $item->InmDescripcion }}

                <x-slot:figure>
                    <img src="{{ asset($item->InmFoto) }}" class="object-cover w-full h-60" alt="{{ $item->InmNombre }}">
                </x-slot:figure>
                <x-slot:menu>
                    <span>
                        S/. {{ calcularPrecioInmueble($item->InmId) }}
                    </span>
                </x-slot:menu>
                <x-slot:actions>
                    <x-button label="Ver inmueble" icon="o-eye" class="btn-primary" wire:click="cargar_inmueble({{ $item->InmId }})" />
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $inmuebles->links() }}
    </div>

    <!-- MODALS -->
    <x-modal wire:model="modalInmueble" title="{{ $titleModal }}" subtitle="{{ $subtitleModal }}" box-class="w-11/12 max-w-7xl" separator>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="col-span-1 p-4 border border-gray-200">
                <x-input
                    label="Cantidad de Meses de Alquiler"
                    required
                    wire:model.live="meses"
                    icon="o-calendar-days"
                    type="number"
                    placeholder="Ingrese los meses de alquiler"
                />
                <x-input
                    label="Fecha de Inicio"
                    required
                    wire:model.live="fecha_inicio"
                    icon="o-calendar"
                    type="date"
                    min="{{ now()->format('Y-m-d') }}"
                />
                <div class="mt-5">
                    <span class="text-sm font-bold">
                        Monto Total:
                    </span>
                    <span>
                        S/. {{ calcularMontoTotalAlquiler($habitaciones) }}
                    </span>
                </div>
                <div class="mt-2">
                    <span class="text-sm font-bold">
                        Monto Mensual:
                    </span>
                    <span>
                        S/. {{ calcularMontoMensualAlquiler($habitaciones, $meses) }}
                    </span>
                </div>
            </div>
            <div class="col-span-2">
                <x-form>
                    @if ($inmueble)
                        @foreach ($inmueble->pisos as $piso)
                            <x-card
                                title="{{ 'Piso N°'.$piso->PisInmNumeroPiso }}"
                                class="border border-gray-200 rounded-none"
                                separator
                            >
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                                    @foreach (
                                        $piso->habitaciones()
                                            ->where('HabInmEstado', true)
                                            ->where('HabInmOcupado', false)
                                            ->get() as $habitacion
                                    )
                                        <div class="px-4 border border-gray-200">
                                            <x-checkbox
                                                wire:model.live="habitaciones"
                                                required
                                                value="{{ $habitacion->HabInmId }}"
                                                id="{{ $habitacion->HabInmId }}"
                                            >
                                                <x-slot:label>
                                                    <x-card wire:key="{{ $habitacion->HabId }}">
                                                        <div class="text-sm font-bold">
                                                            {{ $habitacion->HabInmNombre }}
                                                        </div>
                                                        <span>
                                                            S/. {{ $habitacion->HabInmPrecio }}
                                                        </span>
                                                    </x-card>
                                                </x-slot:label>
                                            </x-checkbox>
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        @endforeach
                    @endif
                </x-form>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalInmueble = false" wire:click="reset_modal_inmueble" />
            <x-button label="{{ $buttonModal }}" class="btn-primary" wire:click="{{ $actionModal }}" />
        </x-slot:actions>
    </x-modal>
</div>
