<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservaAdminController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes');
        $anio = $request->get('anio', date('Y'));

        $query = DB::table('reservas')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'usuarios.id_usuario', '=', 'viviendas.id_propietario')
            ->select(
                'reservas.*',
                'usuarios.nombre',
                'usuarios.apellido_paterno',
                'usuarios.ci',
                'usuarios.telefono',
                'viviendas.nro_casa',
                'areas_recreativas.nombre_area'
            );

        // Filtro por año
        if ($anio) {
            $query->whereYear('reservas.fecha_reserva', $anio);
        }

        // Filtro por mes (si selecciona uno específico)
        if ($mes) {
            $query->whereMonth('reservas.fecha_reserva', $mes);
        }

        $reservas = $query->orderBy('reservas.fecha_reserva', 'desc')->get();

        return view('admin.reservas.index', compact('reservas', 'mes', 'anio'));
    }

    public function aceptar($id)
    {
        DB::table('reservas')->where('id_reserva', $id)->update(['estado_reserva' => 'Aceptado']);
        return redirect()->back()->with('success', 'La solicitud de reserva ha sido ACEPTADA.');
    }

    public function denegar($id)
    {
        DB::table('reservas')->where('id_reserva', $id)->update(['estado_reserva' => 'Denegado']);
        return redirect()->back()->with('error', 'La solicitud de reserva ha sido DENEGADA.');
    }

    /**
     * DESCARGAR REPORTE ANUAL EN PDF
     */
    public function descargarAnual(Request $request)
    {
        $anio = $request->get('anio', date('Y'));

        $reservas = DB::table('reservas')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'usuarios.id_usuario', '=', 'viviendas.id_propietario')
            ->whereYear('reservas.fecha_reserva', $anio)
            ->select(
                'reservas.*',
                'usuarios.nombre',
                'usuarios.apellido_paterno',
                'viviendas.nro_casa',
                'areas_recreativas.nombre_area'
            )
            ->orderBy('reservas.fecha_reserva', 'asc')
            ->get();

        $totalRecaudado = $reservas->where('estado_pago', 'Pagado')->sum('costo_pactado');

        $pdf = Pdf::loadView('admin.reservas.pdf_anual', compact('reservas', 'anio', 'totalRecaudado'));
        return $pdf->setPaper('letter', 'portrait')->download("Reporte_Anual_Reservas_{$anio}.pdf");
    }
}