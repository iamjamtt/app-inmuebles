<?php

use Mary\Traits\Toast;
use App\Models\Inmueble;
use App\Models\TipoInmueble;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

new #[Title('Formulario Inmuebles | App Inmuebles')] class extends Component {
    use Toast, WithFileUploads;

    public $title_component = '';
    public $subtitle_component = '';

    public Inmueble $inmueble;

    public string $modo = 'crear';

    // form
    #[Validate('required|exists:TipoInmueble,TipInmId')]
    public $tipoInmueble = null;
    #[Validate('required|string|max:255')]
    public string $nombre = '';
    #[Validate('required|string|max:255')]
    public string $direccion = '';
    #[Validate('required|string|max:255')]
    public string $descripcion = '';
    #[Validate('required|image|max:4098')]
    public $photo = null;

    public function mount($InmId = null): void
    {
        if ($InmId) {
            $inmueble = Inmueble::find($InmId);
            if (!$inmueble) {
                abort(404, 'Inmueble no encontrado.');
            }
            $this->inmueble = $inmueble;
            $this->title_component = 'Editar Inmueble';
            $this->subtitle_component = 'Aquí puedes editar un inmueble.';
            $this->modo = 'editar';
            $this->tipoInmueble = $this->inmueble->TipInmId;
            $this->nombre = $this->inmueble->InmNombre;
            $this->direccion = $this->inmueble->InmDireccion;
            $this->descripcion = $this->inmueble->InmDescripcion;

        } else {
            $this->title_component = 'Crear Inmueble';
            $this->subtitle_component = 'Aquí puedes crear un nuevo inmueble.';
            $this->modo = 'crear';
        }
    }

    public function saveInmueble()
    {
        $this->validate([
            'tipoInmueble' => 'required|exists:TipoInmueble,TipInmId',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'photo' => $this->modo === 'crear' ? 'required|image|max:4098' : 'nullable|image|max:4098',
        ]);

        if ($this->modo === 'crear') {
            $inmueble = new Inmueble();
        } else {
            $inmueble = $this->inmueble;
        }

        $inmueble->TipInmId = $this->tipoInmueble;
        $inmueble->UsuId = auth()->id();
        $inmueble->InmNombre = $this->nombre;
        $inmueble->InmDireccion = $this->direccion;
        $inmueble->InmDescripcion = $this->descripcion;
        if ($this->photo) {
            $folders = ['inmuebles', 'fotos'];
            $ruta_archivo = $inmueble->InmFoto ? $inmueble->InmFoto : null;
            $inmueble->InmFoto = subirArchivo($this->photo, $ruta_archivo, $folders, null);
        }
        $inmueble->InmFechaCreacion = now();
        $inmueble->save();

        $this->success(
            'Inmueble ' . ($this->modo === 'crear' ? 'creado' : 'actualizado') . ' correctamente.',
            redirectTo: '/inmuebles',
            position: 'toast-top toast-center'
        );
    }

    public function with(): array
    {
        $tipos_inmuebles = TipoInmueble::query()
            ->where('TipInmEstado', true)
            ->get();

        return [
            'tipos_inmuebles' => $tipos_inmuebles,
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

    <!-- FORM -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
        <div class="col-span-2">
            <h2 class="text-lg font-semibold">Datos del Inmueble</h2>
            <span class="text-xs text-gray-500">Los campos marcados con (*) son obligatorios.</span>
        </div>
        <x-file
            label="Foto"
            required
            wire:model.live="photo"
            accept="image/png, image/jpeg, image/jpg"
            hint="Formatos permitidos: PNG, JPEG, JPG"
            crop-after-change
        >
            <img src="{{ $inmueble ? asset($inmueble->InmFoto) : 'https://via.placeholder.com/150' }}" alt="Foto"
                class="h-32 rounded-lg lg:h-48" />
        </x-file>
        <div class="col-span-2">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-select
                    label="Tipo de Inmueble"
                    required
                    :options="$tipos_inmuebles"
                    option-value="TipInmId"
                    option-label="TipInmNombre"
                    placeholder="Seleccione un tipo de inmueble..."
                    wire:model.live="tipoInmueble"
                />
                <x-input
                    label="Nombre"
                    required
                    placeholder="Ingrese el nombre del inmueble..."
                    wire:model.live="nombre"
                />
                <x-input
                    label="Dirección"
                    required
                    placeholder="Ingrese la dirección del inmueble..."
                    wire:model.live="direccion"
                />
                <x-textarea
                    label="Descripción"
                    required
                    placeholder="Ingrese la descripción del inmueble..."
                    wire:model.live="descripcion"
                    rows="3"
                />
            </div>
        </div>
    </div>
    <hr class="my-3">
    <div class="flex justify-end">
        <x-button label="Guardar" class="btn-success" wire:click="saveInmueble" />
    </div>
</div>
