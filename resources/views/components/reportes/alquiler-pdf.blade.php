<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Invoice
    </title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title h2 {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .thank-you {
            margin-top: 50px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h2>
                                    {{ config('app.name') }}
                                </h2>
                            </td>
                            <td>
                                <h2>Cliente</h2>
                                <p>
                                    {{ $persona->PerApellidoPaterno }} {{ $persona->PerApellidoMaterno }}, {{ $persona->PerNombres }}
                                    <br>
                                    {{ $persona->PerDocumentoIdentidad }}
                                    <br>
                                    {{ $persona->PerDireccion }}
                                    <br>
                                    {{ $persona->PerCorreo }}
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Contrato INV/00{{ $alquiler->AlqId }}</td>
                <td></td>
            </tr>
            <tr class="heading">
                <td>Habitaciones</td>
                <td>Precio</td>
            </tr>
            @foreach ($alquiler->detalles as $item)
                <tr class="item">
                    <td>
                        <p>
                            <strong>
                                {{ $item->habitacion_inmueble->HabInmNombre }}
                            </strong>
                            <br>
                            Piso {{ $item->habitacion_inmueble->piso_inmueble->PisInmNumeroPiso }}
                        </p>
                    </td>
                    <td>
                        S/. {{ number_format($item->AlqDetMonto, 2, '.', ',') }}
                    </td>
                </tr>
            @endforeach
            @if ($alquiler->AlqTienePenalidad)
                <tr class="item">
                    <td>
                        <p>
                            <strong>
                                Penalidad
                            </strong>
                            <br>
                            Monto de penalidad
                        </p>
                    </td>
                    <td>
                        S/. {{ number_format($alquiler->AlqMontoPenalidad, 2, '.', ',') }}
                    </td>
                </tr>
            @endif
            @if ($alquiler->AlqTienePenalidad)
                <tr class="total">
                    <td></td>
                    <td>
                        Precio Total: S/. {{ number_format($alquiler->AlqMontoTotal + $alquiler->AlqMontoPenalidad, 2, '.', ',') }}
                    </td>
                </tr>
            @else
                <tr class="total">
                    <td></td>
                    <td>
                        Precio Total: S/. {{ number_format($alquiler->AlqMontoTotal, 2, '.', ',') }}
                    </td>
                </tr>
            @endif
        </table>
        <p class="thank-you">
            ¡Gracias por confiar en nosotros!
        </p>
    </div>
</body>

</html>
