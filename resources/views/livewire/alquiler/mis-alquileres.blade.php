<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\{Alquiler, AlquilerDetalle, PagoMensualidad};
use App\Mail\AlquilerMail;
use Livewire\WithPagination;

new #[Title('Mis Alquileres de Inmuebles')] #[Layout('components.layouts.user')] class extends Component {
    use Toast, WithPagination;

    public array $sortBy = ['column' => 'AlqId', 'direction' => 'asc'];
    public int $cantidadPage = 5;

    public $detalle = [];

    public bool $tienePenalidad = false;
    public string $tipoPenalidad = '';

    // Modal Ver Alquiler
    public bool $modalVerAlquiler = false;
    public string $titleModal = '';
    public string $subtitleModal = '';
    public string $buttonModal = '';
    public string $actionModal = '';

    // Modal Alerta
    public bool $modalAlerta = false;
    public string $titleModalAlerta = '';
    public string $subtitleModalAlerta = '';
    public string $textModalAlerta = '';
    public string $buttonModalAlerta = '';
    public string $actionModalAlerta = '';

    public function mount(): void
    {
        $usuario = auth()->user();
        if ($usuario->rol->RolNombre != 'Cliente') {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
    }

    public function cargarDetalleAlquiler(Alquiler $alquiler): void
    {
        $this->detalle = AlquilerDetalle::query()
            ->where('AlqId', $alquiler->AlqId)
            ->get();

        $this->titleModal = 'Detalle del Alquiler';
        $this->subtitleModal = 'Aquí puedes ver los detalles del alquiler';
        $this->buttonModal = 'Cerrar';
        $this->actionModal = '';
        $this->modalVerAlquiler = true;
    }

    public function alertaCancelarContrato(Alquiler $alquiler): void
    {
        // verificamos si tiene pagos pendientes
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        $tienePagosPendientes = $pagos->count() > 0 ? true : false;

        // verificamos si la fecha actual es mayor a la fecha fin de alquiler
        $fecha_actual = date('Y-m-d');
        $fecha_actual = strtotime($fecha_actual);
        $fecha_fin = $alquiler->AlqFechaFin;
        $fecha_fin = strtotime($fecha_fin);

        $pasoFechaFin = $fecha_actual > $fecha_fin ? true : false;

        $this->tienePenalidad = false;
        $this->tipoPenalidad = '';

        if ($tienePagosPendientes && $pasoFechaFin) {
            $this->textModalAlerta = 'Estas queriendo cancelar el contrato de alquiler, pero tienes pagos pendientes y has pasado la fecha de fin de alquiler, se cobrará una penalidad del 15% del monto total de alquiler. Su penalidad es de S/. ' . (number_format($alquiler->AlqMontoTotal * 0.15, 2, '.', ''));
            $this->tienePenalidad = true;
            $this->tipoPenalidad = '15%';
        } elseif ($tienePagosPendientes && !$pasoFechaFin) {
            $this->textModalAlerta = 'Estas queriendo cancelar el contrato de alquiler, pero tienes pagos pendientes y aun no has pasado la fecha de fin de alquiler, se cobrará una penalidad del 20% del monto total de alquiler. Su penalidad es de S/. ' . (number_format($alquiler->AlqMontoTotal * 0.20, 2, '.', ''));
            $this->tienePenalidad = true;
            $this->tipoPenalidad = '20%';
        }

        // cargar modal de alerta
        $this->titleModalAlerta = 'Cancelar Contrato';
        $this->subtitleModalAlerta = '¿Estás seguro de cancelar el contrato de alquiler?';
        $this->buttonModalAlerta = 'Sí, Cancelar';
        $this->actionModalAlerta = 'cancelarContrato(' . $alquiler . ')';
        $this->modalAlerta = true;
    }

    public function cancelarContrato(Alquiler $alquiler): void
    {
        if ($this->tienePenalidad) {
            if ($this->tipoPenalidad == '15%') {
                $penalidad = $alquiler->AlqMontoTotal * 0.15;
            } elseif ($this->tipoPenalidad == '20%') {
                $penalidad = $alquiler->AlqMontoTotal * 0.20;
            }
        }

        $alquiler->AlqMontoPenalidad = $penalidad;
        $alquiler->AlqTienePenalidad = true;
        $alquiler->AlqEstado = false;
        $alquiler->AlqFinalizado = true;
        $alquiler->save();

        // Verificar si el alquiler tiene pagos pendientes para marcarlos como pagados
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        if ($pagos->count() > 0) {
            foreach ($pagos as $pago) {
                $pago->PagMenMontoPagado = $pago->PagMenMontoPago;
                $pago->PagMenEstado = true;
                $pago->PagMenFechaPago = now();
                $pago->save();
            }
        }

        // A la ultima mensualidad se le cobra la penalidad
        $ultima_mensualidad = PagoMensualidad::query()
            ->where('AlqId', $alquiler->AlqId)
            ->where('PagMenEstado', true)
            ->orderBy('PagMenId', 'desc')
            ->first();

        if ($this->tienePenalidad) {
            $ultima_mensualidad->PagMenMontoPagado = $ultima_mensualidad->PagMenMontoPago + $penalidad;
            $ultima_mensualidad->PagMenEstado = true;
            $ultima_mensualidad->PagMenFechaPago = now();
            $ultima_mensualidad->save();
        }

        // verificamos si todos los pagos estan pagados para desocupar el inmueble, pisos y habitaciones
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        if ($pagos->count() == 0) {
            $detalle = $alquiler->detalles;
            foreach ($detalle as $item) {
                modificamosEstadoOcupado($item->HabInmId, false);
            }
        }

        $this->modalAlerta = false;
        $this->toast('El contrato de alquiler ha sido cancelado.', 'success');
    }

    public function headers(): array
    {
        return [
            ['key' => 'AlqId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'AlqNombre', 'label' => 'Alquiler'],
            ['key' => 'AlqMontoTotal', 'label' => 'Monto Total'],
            ['key' => 'AlqMontoMensual', 'label' => 'Monto Mensual'],
            ['key' => 'AlqCantidadMeses', 'label' => 'Meses', 'sortable' => false],
            ['key' => 'AlqFechaInicio', 'label' => 'F. Inicio', 'sortable' => false],
            ['key' => 'AlqFechaFin', 'label' => 'F. Fin', 'sortable' => false],
            ['key' => 'AlqTienePenalidad', 'label' => 'Penalidad', 'sortable' => false],
            ['key' => 'AlqEstado', 'label' => 'Estado'],
            ['key' => 'accion', 'label' => 'Acciones', 'class' => 'w-48', 'sortable' => false],
        ];
    }

    public function cantidadTabla(): array
    {
        return [
            ['id' => 5, 'name' => '5'],
            ['id' => 10, 'name' => '10'],
            ['id' => 20, 'name' => '20'],
            ['id' => 50, 'name' => '50'],
            ['id' => 100, 'name' => '100'],
        ];
    }

    public function with(): array
    {
        $alquileres = Alquiler::query()
            ->where('ClienteId', auth()->user()->persona->PerId)
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->cantidadPage);

        return [
            'alquileres' => $alquileres,
            'headers' => $this->headers(),
            'cantidadTabla' => $this->cantidadTabla()
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header
        title="Mis Alquileres de Inmuebles"
        subtitle="Aquí puedes ver los inmuebles que tienes alquilados"
        separator
        progress-indicator
    >
    </x-header>

    <!-- TABLE  -->
    <x-card title="Lista de mis Alquileres" class="shadow-lg" shadow separator>
        <x-slot:menu>
            <div class="flex items-center space-x-2">
                <span>Mostrar</span>
                <x-select :options="$cantidadTabla" wire:model.live.debounce="cantidadPage" class="select-sm" />
                <span>registros</span>
            </div>
        </x-slot:menu>
        <x-table :headers="$headers" :rows="$alquileres" :sort-by="$sortBy" with-pagination >
            @scope('cell_AlqId', $item)
            {{ $item->AlqId }}
            @endscope
            @scope('cell_AlqNombre', $item)
            {{ Str::limit($item->AlqNombre, 40)}}
            @endscope
            @scope('cell_AlqMontoTotal', $item)
            {{ 'S/. ' . $item->AlqMontoTotal }}
            @endscope
            @scope('cell_AlqMontoMensual', $item)
            {{ 'S/. ' . $item->AlqMontoMensual }}
            @endscope
            @scope('cell_AlqCantidadMeses', $item)
            {{ $item->AlqCantidadMeses }}
            @endscope
            @scope('cell_AlqFechaInicio', $item)
            {{ convertirFecha($item->AlqFechaInicio) }}
            @endscope
            @scope('cell_AlqFechaFin', $item)
            {{ convertirFecha($item->AlqFechaFin) }}
            @endscope
            @scope('cell_AlqFechaCreacion', $item)
            {{ convertirHoraFecha($item->AlqFechaCreacion) }}
            @endscope
            @scope('cell_AlqTienePenalidad', $item)
            @if($item->AlqTienePenalidad)
                <div class="badge badge-error">
                    Sí
                </div>
                {{ 'S/. ' . $item->AlqMontoPenalidad }}
            @else
                <div class="badge badge-ghost">
                    No
                </div>
            @endif
            @endscope
            @scope('cell_AlqEstado', $item)
            @if($item->AlqEstado && $item->AlqFinalizado)
                <div class="badge badge-success">
                    <x-icon name="o-check" class="w-4 h-4 me-2" />
                    Finalizado
                </div>
            @elseif ($item->AlqEstado && !$item->AlqFinalizado)
                <div class="badge badge-info">
                    <x-icon name="o-check" class="w-4 h-4 me-2" />
                    Alquilado
                </div>
            @elseif (!$item->AlqEstado && $item->AlqFinalizado)
                <div class="badge badge-error">
                    <x-icon name="o-information-circle" class="w-4 h-4 me-2" />
                    Cancelado
                </div>
            @endif
            @endscope
            @scope('cell_accion', $item)
            <x-button
                icon="o-eye"
                class="btn-sm"
                wire:click="cargarDetalleAlquiler({{ $item->AlqId }})"
            />
            <x-button
                icon="o-credit-card"
                class="btn-sm"
                link="/mis-alquileres/{{ $item->AlqId }}/pagos"
            />
            @if (!$item->AlqFinalizado)
            <x-dropdown>
                <x-slot:trigger>
                    <x-button icon="o-ellipsis-horizontal" class="btn-sm" />
                </x-slot:trigger>
                <x-menu-item
                    title="Cancelar Contrato"
                    icon="o-x-mark"
                    wire:click="alertaCancelarContrato({{ $item->AlqId }})"
                />
            </x-dropdown>
            @endif
            @endscope
        </x-table>
    </x-card>

    <!-- MODALS -->
    <x-modal wire:model="modalVerAlquiler" title="{{ $titleModal }}" subtitle="{{ $subtitleModal }}" box-class="w-11/12 max-w-3xl" separator>
        <div>
            @foreach ($detalle as $item)
            <div class="grid grid-cols-2 gap-2 p-4 mb-2 border border-gray-200">
                <div>
                    <span class="font-semibold">Habitación:</span>
                    <span>{{ $item->habitacion_inmueble->HabInmNombre }}</span>
                </div>
                <div>
                    <span class="font-semibold">Piso:</span>
                    <span>N° {{ $item->habitacion_inmueble->piso_inmueble->PisInmNumeroPiso }}</span>
                </div>
                <div>
                    <span class="font-semibold">Tipo de Inmueble:</span>
                    <span>{{ $item->habitacion_inmueble->piso_inmueble->inmueble->tipo_inmueble->TipInmNombre }}</span>
                </div>
                <div>
                    <span class="font-semibold">Costo de Habitación:</span>
                    <span>{{ 'S/. ' . $item->AlqDetMonto }}</span>
                </div>
            </div>
            @endforeach
        </div>

        @if ($actionModal != '')
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalVerAlquiler = false" wire:click="reset_modal_habitacion" />
            <x-button label="{{ $buttonModal }}" class="btn-primary" wire:click="{{ $actionModal }}" />
        </x-slot:actions>
        @endif
    </x-modal>

    <!-- MODALS -->
    <x-modal wire:model="modalAlerta" title="{{ $titleModalAlerta }}" subtitle="{{ $subtitleModalAlerta }}">
        {{ $textModalAlerta }}
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalAlerta = false" />
            <x-button label="{{ $buttonModalAlerta }}" class="btn-success" wire:click="{{ $actionModalAlerta }}" />
        </x-slot:actions>
    </x-modal>
</div>
