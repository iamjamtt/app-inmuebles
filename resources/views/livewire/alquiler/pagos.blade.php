<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\{Alquiler, AlquilerDetalle, PagoMensualidad};
use Livewire\WithPagination;

new #[Title('Mis Pagos de Inmuebles')] #[Layout('components.layouts.user')] class extends Component {
    use Toast, WithPagination;

    public Alquiler $alquiler;

    // Modal Alerta
    public bool $modalAlerta = false;
    public string $titleModalAlerta = '';
    public string $subtitleModalAlerta = '';
    public string $textModalAlerta = '';
    public string $buttonModalAlerta = '';
    public string $actionModalAlerta = '';

    public function mount($AlqId): void
    {
        $this->alquiler = Alquiler::find($AlqId);
        if (!$this->alquiler) {
            abort(404);
        }

        $usuario = auth()->user();
        if ($usuario->rol->RolNombre != 'Cliente') {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
    }

    public function alerta_pago(PagoMensualidad $pago): void
    {
        // verificamos que sea el pago del menor mes
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $this->alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        if ($pagos->count() == 1) {
            $alquiler = $pago->alquiler;
            $fecha_fin = $alquiler->AlqFechaFin;
            $fecha_fin = strtotime($fecha_fin);
            $fecha_actual = now();
            $fecha_actual = strtotime($fecha_actual);

            if ($fecha_actual > $fecha_fin) {
                $this->textModalAlerta = 'El último pago de tu alquiler ha pasado la fecha de fin de alquiler, se cobrará una penalidad del 15% del monto total de alquiler.';
            }
        }

        $pagos = $pagos->sortBy('PagMenId');
        $pago_menor = $pagos->first();

        if ($pago->PagMenId != $pago_menor->PagMenId) {
            $this->error('No puedes realizar el pago de este mes, tienes pagos pendientes.', position: 'toast-top toast-center');
            return;
        }

        // mostramos el modal de alerta
        $this->modalAlerta = true;
        $this->titleModalAlerta = 'Realizar Pago';
        $this->subtitleModalAlerta = '¿Estás seguro de realizar el pago?';
        $this->buttonModalAlerta = 'Realizar Pago';
        $this->actionModalAlerta = 'realizar_pago(' . $pago->PagMenId . ')';
    }

    public function realizar_pago(PagoMensualidad $pago): void
    {
        // verificamos si el ultimo pago paso la fecha fin de alquiler para cobrar una penalidad del 15% del monto total de alquiler
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $this->alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        if ($pagos->count() == 1) {
            $alquiler = $this->alquiler;
            $fecha_fin = $alquiler->AlqFechaFin;
            $fecha_fin = strtotime($fecha_fin);
            $fecha_actual = now();
            $fecha_actual = strtotime($fecha_actual);

            if ($fecha_actual > $fecha_fin) {
                $penalidad = $alquiler->AlqMontoTotal * 0.15;
                $penalidad = round($penalidad, 2);
                $alquiler->AlqMontoPenalidad = $penalidad;
                $alquiler->AlqTienePenalidad = true;
                $alquiler->save();
            }
        }

        // realizamos el pago
        $pago->PagMenMontoPagado = $pago->PagMenMontoPago;
        $pago->PagMenEstado = 1;
        $pago->PagMenFechaPago = now();
        $pago->save();

        // verificamos si todos los pagos estan pagados para desocupar el inmueble, pisos y habitaciones
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $this->alquiler->AlqId)
            ->where('PagMenEstado', false)
            ->get();

        if ($pagos->count() == 0) {
            $alquiler = $this->alquiler;

            $fecha_fin = $alquiler->AlqFechaFin;
            $fecha_fin = strtotime($fecha_fin);
            $fecha_actual = now();
            $fecha_actual = strtotime($fecha_actual);

            if ($fecha_actual > $fecha_fin) {
                $alquiler->AlqFinalizado = true;
                $alquiler->save();
                $detalle = $alquiler->detalles;
                foreach ($detalle as $item) {
                    modificamosEstadoOcupado($item->HabInmId, false);
                }
            }
        }

        $this->modalAlerta = false;
        $this->success('Pago realizado con éxito.', position: 'toast-top toast-center');
    }

    public function headers_pagos(): array
    {
        return [
            ['key' => 'PagMenId', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'PagMenMontoPago', 'label' => 'Monto a Pagar'],
            ['key' => 'PagMenMontoPagado', 'label' => 'Monto Pagado'],
            ['key' => 'PagMenFechaPago', 'label' => 'Fecha de Pago'],
            ['key' => 'PagMenEstado', 'label' => 'Estado'],
            ['key' => 'accion', 'label' => 'Acciones'],
        ];
    }

    public function with(): array
    {
        $pagos = PagoMensualidad::query()
            ->where('AlqId', $this->alquiler->AlqId)
            ->get();

        return [
            'pagos' => $pagos,
            'headers_pagos' => $this->headers_pagos(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header
        title="Mis Pagos de Inmuebles {{ str_replace('Alquiler de Inmueble', '', $alquiler->AlqNombre) }}"
        subtitle="Aquí puedes ver los pagos realizados por tu inmueble."
        separator
        progress-indicator
    >
        <x-slot:actions>
            <x-button label="Regresar" icon="o-arrow-left" link="/mis-alquileres" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="col-span-2">
            <x-card title="Pagos de {{ $alquiler->AlqCantidadMeses }} meses" shadow separator>
                <x-table :headers="$headers_pagos" :rows="$pagos" >
                    @scope('cell_PagMenId', $item)
                    {{ $loop->iteration }}
                    @endscope
                    @scope('cell_PagMenMontoPago', $item)
                    {{ 'S/.' . number_format($item->PagMenMontoPago, 2, '.', ',') }}
                    @endscope
                    @scope('cell_PagMenMontoPagado', $item)
                    {{ 'S/.' . number_format($item->PagMenMontoPagado, 2, '.', ',') }}
                    @endscope
                    @scope('cell_PagMenFechaPago', $item)
                    @if ($item->PagMenFechaPago)
                        {{ convertirHoraFecha($item->PagMenFechaPago) }}
                    @else
                        <div class="badge badge-ghost">
                            Pendiente
                        </div>
                    @endif
                    @endscope
                    @scope('cell_PagMenEstado', $item)
                    @if($item->PagMenEstado)
                        <div class="badge badge-info">
                            <x-icon name="o-check" class="w-4 h-4 me-2" />
                            Pagado
                        </div>
                    @else
                        <div class="badge badge-warning">
                            <x-icon name="o-information-circle" class="w-4 h-4 me-2" />
                            Pendiente
                        </div>
                    @endif
                    @endscope
                    @scope('cell_accion', $item)
                    @if (!$item->PagMenEstado)
                        <x-button
                            icon="o-banknotes"
                            class="btn-sm"
                            wire:click="alerta_pago({{ $item->PagMenId }})"
                            tooltip="Realizar Pago"
                        />
                    @else
                        <div class="badge badge-success">
                            <x-icon name="o-check" class="w-4 h-4 mx-1" />
                        </div>
                    @endif
                    @endscope
                </x-table>
            </x-card>
        </div>
    </div>

    <!-- MODALS -->
    <x-modal wire:model="modalAlerta" title="{{ $titleModalAlerta }}" subtitle="{{ $subtitleModalAlerta }}">
        {{ $textModalAlerta }}
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalAlerta = false" />
            <x-button label="{{ $buttonModalAlerta }}" class="btn-success" wire:click="{{ $actionModalAlerta }}" />
        </x-slot:actions>
    </x-modal>
</div>
