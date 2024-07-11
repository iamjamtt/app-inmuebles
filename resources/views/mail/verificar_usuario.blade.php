<x-mail::message>

@if ($tipo == 'alta')

# Hola {{ $usuario->persona->PerNombre }} {{ $usuario->persona->PerApellidoPaterno }} {{ $usuario->persona->PerApellidoMaterno }}.

Te damos la bienvenida a {{ config('app.name') }}. Te comunica que tu cuenta ha sido dado de alta exitosamente. A continuación, te proporcionamos tus credenciales de acceso:

- **Usuario:** {{ '@'.$usuario->UsuUsername }}
- **Contraseña:** (La contraseña que ingresaste al registrarte).

Para acceder a tu cuenta, haz clic en el siguiente botón:

<x-mail::button :url="route('login')">
    Acceder
</x-mail::button>

@elseif ($tipo == 'baja')

# Hola {{ $usuario->persona->PerNombre }} {{ $usuario->persona->PerApellidoPaterno }} {{ $usuario->persona->PerApellidoMaterno }}.

Te informamos que tu cuenta ha sido dado de baja exitosamente. Si deseas reactivar tu cuenta, por favor, ponte en contacto con el administrador del sistema.

@endif

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
