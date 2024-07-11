<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Inmueble;
use App\Models\TipoInmueble;
use Livewire\WithPagination;

new #[Title('App Inmuebles')] #[Layout('components.layouts.user')] class extends Component {
    use Toast, WithPagination;

    public $tipoInmuebleFiltro = null;

    public Inmueble $inmueble;

    // Modal Inmueble
    public bool $modalInmueble = false;
    public string $titleModal = '';
    public string $subtitleModal = '';
    public string $buttonModal = '';
    public string $actionModal = '';

    public function cargar_inmueble(Inmueble $inmueble): void
    {
        if (auth()->check()) {
            $usuario = auth()->user();
            if ($usuario->rol->RolNombre === 'Cliente') {
                $this->inmueble = $inmueble;
                $this->titleModal = 'Inmueble: ' . $inmueble->InmNombre;
                $this->subtitleModal = 'Aquí puedes ver la información del inmueble.';
                $this->buttonModal = 'Alquilar';
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
        @foreach ($inmuebles as $inmueble)
            <x-card title="{{ $inmueble->InmNombre }}" wire:key="{{ $inmueble->InmId }}">
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
                    <x-button label="Ver inmueble" icon="o-eye" class="btn-primary" wire:click="cargar_inmueble({{ $inmueble->InmId }})" />
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $inmuebles->links() }}
    </div>

    <!-- MODALS -->
    <x-modal wire:model="modalInmueble" title="{{ $titleModal }}" subtitle="{{ $subtitleModal }}" box-class="w-11/12 max-w-5xl" separator>
        <x-form>
            {{-- <x-input label="Nombre" required wire:model="nombre_habitacion" placeholder="Ingrese el nombre de la habitación" />
            <x-input label="Precio" required wire:model="precio_habitacion" money locale="es-PE" /> --}}
        </x-form>

        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.modalInmueble = false" wire:click="reset_modal_inmueble" />
            <x-button label="{{ $buttonModal }}" class="btn-primary" wire:click="{{ $actionModal }}" />
        </x-slot:actions>
    </x-modal>
</div>
