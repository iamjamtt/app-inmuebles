<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\TipoEvidencia;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use App\Mail\RegistroUsuarioMail;

new #[Title('Registro de Clientes')] #[Layout('components.layouts.auth')] class extends Component {
    use Toast, WithFileUploads;

    // variables del formulario
    #[Validate('required|numeric|digits:8')]
    public string $documento = '';
    #[Validate('required|string|max:50')]
    public string $nombre = '';
    #[Validate('required|string|max:50')]
    public string $apellido_paterno = '';
    #[Validate('required|string|max:50')]
    public string $apellido_materno = '';
    #[Validate('required|date')]
    public string $fecha_nacimiento = '';
    #[Validate('required|string|max:1')]
    public string $sexo = '';
    #[Validate('required|string|max:100')]
    public string $direccion = '';
    #[Validate('required|email')]
    public string $email = '';
    #[Validate('required|string|min:8')]
    public string $password = '';
    #[Validate('required|exists:TipoEvidencia,TipEviId')]
    public $tipo_evidencia = null;
    #[Validate('required|file|mimes:pdf')]
    public $evidencia = null;


    public function registrar(): void
    {
        $this->validate();

        $persona = new Persona();
        $persona->PerDocumentoIdentidad = $this->documento;
        $persona->PerApellidoPaterno = $this->apellido_paterno;
        $persona->PerApellidoMaterno = $this->apellido_materno;
        $persona->PerNombres = $this->nombre;
        $persona->PerFechaNacimiento = $this->fecha_nacimiento;
        $persona->PerSexo = $this->sexo;
        $persona->PerCorreo = $this->email;
        $persona->PerDireccion = $this->direccion;
        $persona->PerEstado = true;
        $persona->PerFechaCreacion = now();
        $persona->TipEviId = $this->tipo_evidencia;
        if ($this->evidencia) {
            $folders = ['evidencias', 'personas'];
            $ruta_archivo = $persona->PerTipoEvidenciaArchivo ? $persona->PerTipoEvidenciaArchivo : null;
            $persona->PerTipoEvidenciaArchivo = subirArchivo($this->evidencia, $ruta_archivo, $folders, null, null);
        }
        $persona->save();

        $usuario = new Usuario();
        $usuario->UsuUsername = strtolower(str_replace(' ', '', $this->nombre));
        $usuario->UsuContrasena = Hash::make($this->password);
        $usuario->RolId = 3;
        $usuario->PerId = $persona->PerId;
        $usuario->UsuFechaCreacion = now();
        $usuario->save();

        // obtenemos el correo del administrador para enviar el correo que hay un nuevo usuario registrado
        $admini_correo = 'admin@app.inmueble';
        Mail::to($usuario->persona->PerCorreo)->send(new RegistroUsuarioMail());

        $this->success(
            '¡Bienvenido!',
            'Has registrado tu cuenta correctamente. Su usuario es: ' . $usuario->UsuUsername,
            redirectTo: '/',
            position: 'toast-top toast-center'
        );
    }

    public function with(): array
    {
        $sexos = [
            ['id' => 'M', 'nombre' => 'Masculino'],
            ['id' => 'F', 'nombre' => 'Femenino'],
        ];

        $tipos_evidencias = TipoEvidencia::query()
            ->where('TipEviEstado', true)
            ->get();

        return [
            'sexos' => $sexos,
            'tipos_evidencias' => $tipos_evidencias,
        ];
    }
}; ?>

<div class="mx-auto mt-10 md:max-w-4xl">
    <div class="flex justify-center mb-10">
        <x-app-inmueble />
    </div>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-center">
            Registrate
        </h1>
    </div>

    <x-form wire:submit="registrar">
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Numero de Documento" wire:model.live="documento" icon="o-credit-card" type="number" placeholder="Ingrese su número de documento" />
            <x-input label="Nombres" wire:model.live="nombre" icon="o-user" placeholder="Ingrese su nombre" />
            <x-input label="Apellido Paterno" wire:model.live="apellido_paterno" icon="o-user" placeholder="Ingrese su apellido paterno" />
            <x-input label="Apellido Materno" wire:model.live="apellido_materno" icon="o-user" placeholder="Ingrese su apellido materno" />
            <x-input label="Fecha de Nacimiento" wire:model.live="fecha_nacimiento" icon="o-calendar" type="date" />
            <x-select label="Sexo" wire:model.live="sexo" :options="$sexos" option-value="id" option-label="nombre" placeholder="Seleccione su sexo" />
            <div class="col-span-2">
                <x-input label="Dirección" wire:model.live="direccion" icon="o-map-pin" placeholder="Ingrese su dirección" />
            </div>
            <x-input label="Correo Electrónico" wire:model.live="email" icon="o-envelope" type="email" placeholder="Ingrese su correo electrónico" />
            <x-input label="Contraseña" wire:model.live="password" icon="o-lock-closed" type="password" placeholder="Ingrese su contraseña" />
            <div class="space-y-2">
                @foreach ($tipos_evidencias as $tipo_evidencia)
                    <label for="{{ $tipo_evidencia->TipEviId }}" class="flex items-center space-x-2" wire:key="{{ $tipo_evidencia->TipEviId }}">
                        <input type="radio" wire:model.live="tipo_evidencia" value="{{ $tipo_evidencia->TipEviId }}" class="radio radio-info" id="{{ $tipo_evidencia->TipEviId }}" />
                        <span>
                            {{ $tipo_evidencia->TipEviNombre }}
                        </span>
                    </label>
                @endforeach
            </div>
            <x-file label="Evidencia" wire:model.live="evidencia" accept="application/pdf" hint="Solo se aceptan archivos en formato PDF" />
        </div>

        <x-slot:actions>
            <x-button label="Registrar" class="w-full btn-primary" icon="o-plus" type="submit" spinner="registrar" />
        </x-slot:actions>
    </x-form>
<div>
