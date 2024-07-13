<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AlquilerController extends Controller
{
    public function pdf($AlqId)
    {
        $alquiler = Alquiler::find($AlqId);
        $persona = $alquiler->cliente;

        $data = [
            'alquiler' => $alquiler,
            'persona' => $persona,
        ];

        $pdf = Pdf::loadView('components.reportes.alquiler-pdf', $data);

        return $pdf->stream('alquiler.pdf');
    }
}
