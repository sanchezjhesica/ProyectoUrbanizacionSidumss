<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lectura;
use App\Models\Vivienda;
use App\Models\CobroAgua;
use App\Models\CobroMantenimiento;
use App\Models\CobroRemesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LecturaController extends Controller
{
    public function index(Request $request)
    {
        $viviendas = Vivienda::orderBy('nro_casa', 'asc')->get();
        $id_vivienda = $request->get('id_vivienda');

        $queryAgua = CobroAgua::with(['vivienda.propietario', 'lectura']);
        if($id_vivienda) { $queryAgua->where('id_vivienda', $id_vivienda); }
        $cobrosAgua = $queryAgua->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        // =========================================================================
        // CORREGIDO: Sumar reservas vinculadas a ESTA CASA ESPECÍFICA (id_vivienda)
        // =========================================================================
        foreach ($cobrosAgua as $a) {
            $monto_reservas = DB::table('reservas')
                ->where('id_vivienda', $a->id_vivienda) // <-- FILTRO POR CASA ESPECÍFICA
                ->whereMonth('fecha_reserva', $a->mes)
                ->whereYear('fecha_reserva', $a->anio)
                ->where('estado_reserva', 'Aceptado')
                ->sum('costo_pactado') ?? 0;

            $a->total_real = $a->total_pagar + $monto_reservas;
            $a->monto_reservas = $monto_reservas;
        }

        $queryMante = CobroMantenimiento::with('vivienda.propietario');
        if($id_vivienda) { $queryMante->where('id_vivienda', $id_vivienda); }
        $cobrosMante = $queryMante->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        $queryRemesas = CobroRemesa::with(['vivienda.propietario']);
        if($id_vivienda) { $queryRemesas->where('id_vivienda', $id_vivienda); }

        $cobrosRemesas = $queryRemesas->select(
                'id_cobro_remesa as id_referencia',
                'id_vivienda', 
                'mes', 
                'anio', 
                'estado_pago', 
                'total_remesa as total_mes'
            )
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('admin.lecturas.index', compact('cobrosAgua', 'cobrosMante', 'cobrosRemesas', 'viviendas'));
    }

    public function create()
    {
        $viviendas = Vivienda::all();
        return view('admin.lecturas.create', compact('viviendas'));
    }

    public function showRecibo($id_cobro_agua)
    {
        $cobroAgua = CobroAgua::with(['vivienda.propietario', 'lectura'])->findOrFail($id_cobro_agua);
        $vivienda  = $cobroAgua->vivienda;

        $cobroMante = CobroMantenimiento::where('id_vivienda', $vivienda->id_vivienda)
            ->where('mes', $cobroAgua->mes)
            ->where('anio', $cobroAgua->anio)
            ->first();

        // Reservas de esta vivienda específica
        $monto_reservas = DB::table('reservas')
            ->where('id_vivienda', $vivienda->id_vivienda) // <-- FILTRO POR CASA ESPECÍFICA
            ->whereMonth('fecha_reserva', $cobroAgua->mes)
            ->whereYear('fecha_reserva', $cobroAgua->anio)
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

        return view('admin.lecturas.recibo', compact('cobroAgua', 'cobroMante'))->with(['monto_wally' => $monto_reservas]);
    }

public function imprimirAgua($id)
    {
        $cobro = CobroAgua::with(['vivienda.propietario', 'lectura'])->findOrFail($id);
        
        // CORREGIDO: Busca las reservas de esta casa y unifica el nombre $montoReservas
        $montoReservas = DB::table('reservas')
            ->where('id_vivienda', $cobro->id_vivienda)
            ->whereMonth('fecha_reserva', $cobro->mes)
            ->whereYear('fecha_reserva', $cobro->anio)
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

        // Pasamos tanto 'montoReservas' como 'monto_reservas' para evitar cualquier error en el Blade
        return Pdf::loadView('admin.lecturas.recibo_agua', [
            'cobro'          => $cobro,
            'montoReservas'  => $montoReservas,
            'monto_reservas' => $montoReservas
        ])->setPaper('letter')->download("Recibo_Agua_Casa_{$cobro->vivienda->nro_casa}.pdf");
    }

    public function imprimirMantenimiento($id)
    {
        $cobro = CobroMantenimiento::with(['vivienda.propietario'])->findOrFail($id);
        return Pdf::loadView('admin.lecturas.recibo_mantenimiento', compact('cobro'))
            ->setPaper('letter')->download("Mantenimiento_Casa_{$cobro->vivienda->nro_casa}.pdf");
    }

    public function imprimirRemesas($id)
    {
        $cobro = CobroRemesa::with(['vivienda.propietario'])->findOrFail($id);
        $detallesRemesas = [$cobro]; 

        return Pdf::loadView('admin.lecturas.recibo_remesas', compact('detallesRemesas', 'cobro'))
            ->setPaper('letter')->download("Remesas_Casa_{$cobro->vivienda->nro_casa}.pdf");
    }
}