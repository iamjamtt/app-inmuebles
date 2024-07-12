<?php

use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

new #[Title('Perfil')] #[Layout('components.layouts.user')] class extends Component {
    use Toast;

    public $UsuId;
    public Usuario $usuario;

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
    #[Validate('required|string|max:50')]
    public string $username = '';
    #[Validate('nullable|string|min:8|max:50')]
    public string $password = '';

    public function mount($UsuId): void
    {
        $this->UsuId = $UsuId;
        $this->usuario = Usuario::find($UsuId);

        if (!$this->usuario) {
            abort(404);
        }

        if ($this->usuario->UsuId != auth()->user()->UsuId) {
            abort(403, 'No tienes permisos para acceder a esta página');
        }

        if ($this->usuario->rol->RolNombre != 'Cliente') {
            abort(403, 'No tienes permisos para acceder a esta página');
        }

        $this->documento = $this->usuario->persona->PerDocumentoIdentidad;
        $this->nombre = $this->usuario->persona->PerNombres;
        $this->apellido_paterno = $this->usuario->persona->PerApellidoPaterno;
        $this->apellido_materno = $this->usuario->persona->PerApellidoMaterno;
        $this->fecha_nacimiento = date('Y-m-d', strtotime($this->usuario->persona->PerFechaNacimiento));
        $this->sexo = $this->usuario->persona->PerSexo;
        $this->direccion = $this->usuario->persona->PerDireccion;
        $this->email = $this->usuario->persona->PerCorreo;
        $this->username = $this->usuario->UsuUsername;
    }

    public function save(): void
    {
        $this->validate([
            'documento' => 'required|numeric|digits:8',
            'nombre' => 'required|string|max:50',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:1',
            'direccion' => 'required|string|max:100',
            'email' => 'required|email|unique:persona,PerCorreo,' . $this->usuario->persona->PerId . ',PerId',
            'username' => 'required|string|max:50',
            'password' => 'nullable|string|min:8|max:50',
        ]);

        $this->usuario->persona->PerDocumentoIdentidad = $this->documento;
        $this->usuario->persona->PerNombres = $this->nombre;
        $this->usuario->persona->PerApellidoPaterno = $this->apellido_paterno;
        $this->usuario->persona->PerApellidoMaterno = $this->apellido_materno;
        $this->usuario->persona->PerFechaNacimiento = $this->fecha_nacimiento;
        $this->usuario->persona->PerSexo = $this->sexo;
        $this->usuario->persona->PerDireccion = $this->direccion;
        $this->usuario->persona->PerCorreo = $this->email;
        $this->usuario->UsuUsername = $this->username;
        if ($this->password) {
            $this->usuario->UsuContrasena = Hash::make($this->password);
        }
        $this->usuario->persona->save();
        $this->usuario->save();

        $this->success(
            '¡Excelente!',
            'Tus datos han sido actualizados correctamente',
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

        return [
            'sexos' => $sexos,
        ];
    }
}; ?>

<div>
    <div class="mb-5">
        <h1 class="text-xl font-bold text-center">
            Perfil de {{ $usuario->persona->PerNombres }} {{ $usuario->persona->PerApellidoPaterno }} {{ $usuario->persona->PerApellidoMaterno }}
        </h1>
    </div>

    <x-form wire:submit="save">
        <div class="grid grid-cols-2 gap-4">
            <div class="grid grid-cols-2 gap-4 p-5 bg-white border border-gray-200 shadow-sm">
                <x-input label="Numero de Documento" wire:model.live="documento" icon="o-credit-card" type="number" placeholder="Ingrese su número de documento" />
                <x-input label="Nombres" wire:model.live="nombre" icon="o-user" placeholder="Ingrese su nombre" />
                <x-input label="Apellido Paterno" wire:model.live="apellido_paterno" icon="o-user" placeholder="Ingrese su apellido paterno" />
                <x-input label="Apellido Materno" wire:model.live="apellido_materno" icon="o-user" placeholder="Ingrese su apellido materno" />
                <x-input label="Fecha de Nacimiento" wire:model.live="fecha_nacimiento" icon="o-calendar" type="date" />
                <x-select label="Sexo" wire:model.live="sexo" :options="$sexos" option-value="id" option-label="nombre" placeholder="Seleccione su sexo" />
                <div class="col-span-2">
                    <x-input label="Dirección" wire:model.live="direccion" icon="o-map-pin" placeholder="Ingrese su dirección" />
                </div>
                <div class="col-span-2">
                    <x-input label="Correo Electrónico" wire:model.live="email" icon="o-envelope" type="email" placeholder="Ingrese su correo electrónico" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 p-5 bg-white border border-gray-200 shadow-sm">
                <x-input label="Usuario" wire:model.live="username" icon="o-user" placeholder="Ingrese su usuario" />
                <x-input label="Contraseña" wire:model.live="password" icon="o-lock-closed" type="password" placeholder="Ingrese su contraseña" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Guardar" class="w-full btn-primary" icon="o-check" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>
