<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Inmueble;
use Livewire\WithPagination;

new #[Title('App Inmuebles')] #[Layout('components.layouts.user')] class extends Component {
    use WithPagination;

    public function with(): array
    {
        $inmuebles = Inmueble::query()
            ->where('InmEstado', true)
            ->where('InmOcupado', false)
            ->paginate(6);
        return [
            'inmuebles' => $inmuebles,
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Inmuebles" subtitle="Aquí puedes encontrar los inmuebles que tenemos disponibles para ti." separator
        progress-indicator>
        {{-- <x-slot:middle class="!justify-end">
            <x-input placeholder="Buscar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle> --}}
        {{-- <x-slot:actions>
            <x-select :options="$estados" wire:model.live="estadoFiltro" />
        </x-slot:actions> --}}
    </x-header>

    {{-- mostrar card de inmuebles para el usuario --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($inmuebles as $inmueble)
            <x-card title="{{ $inmueble->InmNombre }}">
                {{ $inmueble->InmDescripcion }}

                <x-slot:figure>
                    <img src="{{ asset($inmueble->InmFoto) }}" class="object-cover w-full h-60" alt="{{ $inmueble->InmNombre }}">
                </x-slot:figure>
                <x-slot:menu>
                    <span>
                        S/. {{ calcularPrecioInmueble($inmueble->InmId) }}
                    </span>
                </x-slot:menu>
                <x-slot:actions>
                    <x-button label="Ver inmueble" icon="o-eye" class="btn-primary" />
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $inmuebles->links() }}
    </div>
</div>
