<x-mail::message>
# Hola {{ $usuario->persona->PerNombres }} {{ $usuario->persona->PerApellidoPaterno }} {{ $usuario->persona->PerApellidoMaterno }}.

Tu alquiler ha finalizado con éxito.

## Detalles del alquiler
- **Fecha de inicio:** {{ convertirFecha($alquiler->AlqFechaInicio) }}
- **Fecha de fin:** {{ convertirFecha($alquiler->AlqFechaFin) }}
- **Total a pagar:** S/. {{ $alquiler->AlqMontoTotal }}
- **Monto mensual:** S/. {{ $alquiler->AlqMontoMensual }}

Para más detalles, puedes ver el alquiler en la plataforma. Si tienes alguna duda, no dudes en contactarnos.

<x-mail::button :url="route('home.usuario')">
Ver alquiler
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
