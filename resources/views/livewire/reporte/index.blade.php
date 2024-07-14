<?php

use Mary\Traits\Toast;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;

new #[Title('Reportes | App Inmuebles')] class extends Component {
    use Toast, WithPagination;

    #[Url('tipRep')]
    public $reporte = null;

    public $fecha = null;

    public string $fecha_inicio = '';
    public string $fecha_fin = '';

    public bool $mostrar = false;

    public Collection $data;

    public function mount(): void
    {
        $usuario = getAuthUsuario();
        if ($usuario->rol->RolNombre !== 'Administrador') {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        $this->data = new Collection();
    }

    public function updatedReporte(): void
    {
        $this->limpiar();
    }

    public function limpiar(): void
    {
        $this->fecha = null;
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->mostrar = false;
        $this->data = new Collection();
    }

    public function filtrar(): void
    {
        if ($this->fecha == null) {
            $this->error('Debe seleccionar una fecha.', position: 'toast-top toast-center');
            return;
        }

        $fecha = explode(' to ', $this->fecha);
        $fecha_inicio = $fecha[0];
        $fecha_inicio = str_replace('/', '-', $fecha_inicio);
        $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio));
        $this->fecha_inicio = $fecha_inicio;
        $fecha_fin = $fecha[1];
        $fecha_fin = str_replace('/', '-', $fecha_fin);
        $fecha_fin = date('Y-m-d', strtotime($fecha_fin));
        $this->fecha_fin = $fecha_fin;

        if ($this->reporte == 1) {
            $this->data = DB::table('alquiler')
                ->whereBetween('AlqFechaCreacion', [$this->fecha_inicio, $this->fecha_fin])
                ->where('AlqEstado', true)
                ->select(DB::raw('AlqNombre as name, count(*) as cantidad'))
                ->groupBy('AlqNombre')
                ->orderBy('cantidad', 'desc')
                ->get();
        } elseif ($this->reporte == 2) {
            $this->data = DB::table('alquiler')
                ->whereBetween('AlqFechaCreacion', [$this->fecha_inicio, $this->fecha_fin])
                ->where('AlqEstado', false)
                ->select(DB::raw('AlqNombre as name, count(*) as cantidad'))
                ->groupBy('AlqNombre')
                ->orderBy('cantidad', 'desc')
                ->get();
        } elseif ($this->reporte == 3) {
            $this->data = DB::table('inmueble')
                ->whereBetween('InmFechaDadoAlta', [$this->fecha_inicio, $this->fecha_fin])
                ->where('InmEstado', true)
                ->select(DB::raw('InmNombre as name, InmFechaDadoAlta as fecha'))
                ->orderBy('InmFechaDadoAlta', 'desc')
                ->get();
        } elseif ($this->reporte == 4) {
            $this->data = DB::table('inmueble')
                ->whereBetween('InmFechaDadoBaja', [$this->fecha_inicio, $this->fecha_fin])
                ->where('InmEstado', false)
                ->select(DB::raw('InmNombre as name, InmFechaDadoBaja as fecha'))
                ->orderBy('InmFechaDadoBaja', 'desc')
                ->get();
        } elseif ($this->reporte == 5) {
            $this->data = DB::table('alquiler')
                ->whereBetween('AlqFechaCreacion', [$this->fecha_inicio, $this->fecha_fin])
                ->select(DB::raw('AlqNombre as name, count(*) as cantidad'))
                ->groupBy('AlqNombre')
                ->orderBy('cantidad', 'desc')
                ->get();
        }

        $this->mostrar = true;
    }

    public function headers(): array
    {
        if ($this->reporte == 3 || $this->reporte == 4) {
            return [
                ['key' => 'id', 'label' => '#', 'class' => 'w-1', 'sortable' => false],
                ['key' => 'name', 'label' => 'Nombre', 'sortable' => false],
                ['key' => 'fecha', 'label' => 'Fecha', 'sortable' => false],
            ];
        } else {
            return [
                ['key' => 'id', 'label' => '#', 'class' => 'w-1', 'sortable' => false],
                ['key' => 'name', 'label' => 'Nombre', 'sortable' => false],
                ['key' => 'cantidad', 'label' => 'Cantidad', 'sortable' => false],
            ];
        }
    }

    public function with(): array
    {
        $selects = [
            ['id' => 1, 'name' => 'Reporte de Bienes Alquilados'],
            ['id' => 2, 'name' => 'Reporte de Bienes Desalquilados'],
            ['id' => 3, 'name' => 'Reporte de Bienes Dado de Alta'],
            ['id' => 4, 'name' => 'Reporte de Bienes Dado de Baja'],
            ['id' => 5, 'name' => 'Reporte Estadistico del Bien mas Alquilado'],
        ];

        $configDate = [
            'altFormat' => 'd/m/Y',
            'mode' => 'range',
        ];

        return [
            'selects' => $selects,
            'configDate' => $configDate,
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header
        title="Reportes"
        subtitle="Aquí puedes visualizar los reportes generados por el sistema."
        separator progress-indicator
    >
    </x-header>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-select
            label="Seleccione un tipo reporte"
            :options="$selects"
            option-value="id"
            option-label="name"
            placeholder="Seleccione un tipo de reporte"
            wire:model.live="reporte"
        />
    </div>

    @if ($reporte)
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-datepicker label="Ingrese la fecha" wire:model="fecha" icon="o-calendar" :config="$configDate" />
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-button label="Filtrar" wire:click="filtrar" class="mt-7 btn-primary" />
                    <x-button label="Limpiar" wire:click="limpiar" class="mt-7 btn-outline" />
                </div>
                @if ($mostrar)
                    <div class="col-span-2">
                        <x-card shadow separator>
                            <x-table :headers="$headers" :rows="$data" >
                                @scope('cell_id', $item)
                                {{ $loop->iteration }}
                                @endscope
                                @scope('cell_name', $item)
                                {{ $item->name }}
                                @endscope
                                @if ($reporte == 3 || $reporte == 4)
                                    @scope('cell_fecha', $item)
                                    {{ convertirHoraFecha($item->fecha) }}
                                    @endscope
                                @else
                                    @scope('cell_cantidad', $item)
                                    <span class="font-bold">
                                        {{ $item->cantidad }}
                                    </span>
                                    @endscope
                                @endif
                            </x-table>
                        </x-card>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
