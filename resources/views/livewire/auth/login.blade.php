<?php

use App\Models\Usuario;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

new #[Title('Login')] #[Layout('components.layouts.auth')] class extends Component {
    use Toast;

    #[Validate('required|string')]
    public string $username = '';
    #[Validate('required|string')]
    public string $contraseña = '';

    public function login()
    {
        $this->validate();

        $username = str_replace('@', '', $this->username);

        $usuario = Usuario::query()
            ->where('UsuUsername', $username)
            ->first();

        if ($usuario && $usuario->UsuEstado === false) {
            $this->error('Usuario deshabilitado', 'Tu cuenta ha sido dado de baja, por favor, contacta al administrador.', position: 'toast-top toast-center');
            return;
        }

        if (!$usuario || !Hash::check($this->contraseña, $usuario->UsuContrasena)) {
            $this->error('Credenciales incorrectas', 'Por favor, verifica tus credenciales e intenta nuevamente.', position: 'toast-top toast-center');
            return;
        }

        auth()->login($usuario);

        $this->info(
            '¡Bienvenido!',
            'Has iniciado sesión correctamente.',
            redirectTo: '/inicio',
            position: 'toast-top toast-center'
        );
    }
}; ?>

<div class="md:w-96 mx-auto mt-20">
    <div class="mb-10 flex justify-center">
        <x-app-inmueble />
    </div>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-center">
            Iniciar sesión
        </h1>
    </div>

    <x-form wire:submit="login">
        <x-input label="Username" wire:model.live="username" icon="o-user" placeholder="{{ '@' }}example" />
        <x-input label="Contraseña" wire:model.live="contraseña" type="password" icon="o-key" placeholder="********" />

        <x-slot:actions>
            <x-button label="Ingresar" class="btn-primary w-full" icon="o-paper-airplane" type="submit" spinner="login" />
        </x-slot:actions>
    </x-form>
<div>
