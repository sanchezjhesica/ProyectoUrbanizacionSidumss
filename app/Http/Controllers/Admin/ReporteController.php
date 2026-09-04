<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vivienda;
use App\Models\User;
use App\Models\Egreso;
use App\Models\Lectura;
use App\Models\CobroAgua;
use App\Models\CobroMantenimiento;
use App\Models\CobroRemesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Reporte General (Vista Web)
     */
    public function index()
    {
        $ingAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('total_remesa');
        
        $totalIngresos = $ingAgua + $ingMante + $ingRemesas;
        $totalEgresos = Egreso::sum('monto');
        $saldoCaja = $totalIngresos - $totalEgresos;
        
        $totalPendiente = CobroAgua::where('estado_pago', 'Pendiente')->sum('total_pagar');
        $totalUsuarios = User::where('id_rol', 3)->count();

        $detallesAgua = CobroAgua::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Agua '.$i->mes.'/'.$i->anio, 'monto' => $i->total_pagar ];
        });
        $detallesMante = CobroMantenimiento::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Mante. '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_fijo ];
        });
        $detallesRemesas = CobroRemesa::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Remesa '.$i->mes.'/'.$i->anio, 'monto' => $i->total_remesa ];
        });

        $listaIngresos = $detallesAgua->concat($detallesMante)->concat($detallesRemesas)->sortByDesc('fecha')->take(20);
        $listaEgresos = Egreso::orderBy('fecha_egreso', 'desc')->take(20)->get();

        return view('admin.reportes.index', compact('totalIngresos', 'totalEgresos', 'saldoCaja', 'totalPendiente', 'totalUsuarios', 'listaIngresos', 'listaEgresos'));
    }

    /**
     * Descargar Reporte General en PDF
     */
    public function descargarGeneral()
    {
        $ingAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('total_remesa');
        $totalIngresos = $ingAgua + $ingMante + $ingRemesas;
        $totalEgresos = Egreso::sum('monto');
        $saldoCaja = $totalIngresos - $totalEgresos;

        $detallesAgua = CobroAgua::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Agua '.$i->mes.'/'.$i->anio, 'monto' => $i->total_pagar ];
        });
        $detallesMante = CobroMantenimiento::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Mante. '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_fijo ];
        });
        $detallesRemesas = CobroRemesa::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa, 'concepto' => 'Remesa '.$i->mes.'/'.$i->anio, 'monto' => $i->total_remesa ];
        });

        $listaIngresos = $detallesAgua->concat($detallesMante)->concat($detallesRemesas)->sortByDesc('fecha')->take(50);
        $listaEgresos = Egreso::orderBy('fecha_egreso', 'desc')->take(50)->get();

        $pdf = Pdf::loadView('admin.reportes.general_pdf', compact('totalIngresos', 'totalEgresos', 'saldoCaja', 'listaIngresos', 'listaEgresos'));
        return $pdf->setPaper('letter', 'portrait')->download('Reporte_General_SIDUMSS.pdf');
    }

    /**
     * Reporte por Vivienda (Vista Web)
     */
    public function porVivienda(Request $request)
    {
        $viviendas = Vivienda::with('propietario')->get();
        $viviendaSeleccionada = null;
        $pagosAgua = []; $pagosMante = []; $pagosRemesas = []; $historialLecturas = [];

        if ($request->has('id_vivienda')) {
            $id = $request->id_vivienda;
            $viviendaSeleccionada = Vivienda::with('propietario')->findOrFail($id);
            $pagosAgua = CobroAgua::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $pagosMante = CobroMantenimiento::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $pagosRemesas = CobroRemesa::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $historialLecturas = Lectura::where('id_vivienda', $id)->orderBy('id_lectura', 'desc')->get();
        }

        return view('admin.reportes.vivienda', compact('viviendas', 'viviendaSeleccionada', 'pagosAgua', 'pagosMante', 'pagosRemesas', 'historialLecturas'));
    }

    /**
     * DESCARGAR REPORTE POR VIVIENDA (ESTE ES EL QUE FALTABA)
     */
    public function descargarPorVivienda($id)
    {
        $vivienda = Vivienda::with('propietario')->findOrFail($id);
        
        $pagosAgua = CobroAgua::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        $pagosMante = CobroMantenimiento::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        $pagosRemesas = CobroRemesa::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        $historialLecturas = Lectura::where('id_vivienda', $id)->orderBy('id_lectura', 'desc')->take(12)->get();

        $pdf = Pdf::loadView('admin.reportes.vivienda_pdf', compact(
            'vivienda', 'pagosAgua', 'pagosMante', 'pagosRemesas', 'historialLecturas'
        ));

        return $pdf->setPaper('letter', 'portrait')->download("Reporte_Casa_{$vivienda->nro_casa}.pdf");
    }

    /**
     * Reporte de Morosidad (Vista Web)
     */
public function morosidad()
{
    // 1. Obtener todos los recibos pendientes de las 3 tablas
    $pendientesAgua = CobroAgua::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
    $pendientesMante = CobroMantenimiento::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
    $pendientesRemesas = CobroRemesa::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();

    // 2. Unificar todo en una sola colección
    $todoPendiente = collect();

    foreach($pendientesAgua as $a) {
        $todoPendiente->push((object)[
            'mes' => $a->mes, 'anio' => $a->anio, 'monto' => $a->total_pagar,
            'casa' => $a->vivienda->nro_casa, 'propietario' => $a->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Agua Potable'
        ]);
    }
    foreach($pendientesMante as $m) {
        $todoPendiente->push((object)[
            'mes' => $m->mes, 'anio' => $m->anio, 'monto' => $m->monto_fijo,
            'casa' => $m->vivienda->nro_casa, 'propietario' => $m->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Mantenimiento'
        ]);
    }
    foreach($pendientesRemesas as $r) {
        $todoPendiente->push((object)[
            'mes' => $r->mes, 'anio' => $r->anio, 'monto' => $r->total_remesa,
            'casa' => $r->vivienda->nro_casa, 'propietario' => $r->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Expensas/Remesas'
        ]);
    }

    // 3. AGRUPAR POR AÑO Y LUEGO POR MES
    $morosidadPorMes = $todoPendiente->groupBy([
        'anio',
        function ($item) { return $item->mes; }
    ]);

    $totalDeudaGlobal = $todoPendiente->sum('monto');

    return view('admin.reportes.morosidad', compact('morosidadPorMes', 'totalDeudaGlobal'));
}
public function descargarMorosidad()
{
    // 1. Recolectar todos los pendientes
    $pendientesAgua = CobroAgua::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
    $pendientesMante = CobroMantenimiento::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
    $pendientesRemesas = CobroRemesa::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();

    $todoPendiente = collect();

    foreach($pendientesAgua as $a) {
        $todoPendiente->push((object)[
            'mes' => $a->mes, 'anio' => $a->anio, 'monto' => $a->total_pagar,
            'casa' => $a->vivienda->nro_casa, 'propietario' => $a->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Agua Potable'
        ]);
    }
    foreach($pendientesMante as $m) {
        $todoPendiente->push((object)[
            'mes' => $m->mes, 'anio' => $m->anio, 'monto' => $m->monto_fijo,
            'casa' => $m->vivienda->nro_casa, 'propietario' => $m->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Mantenimiento'
        ]);
    }
    foreach($pendientesRemesas as $r) {
        $todoPendiente->push((object)[
            'mes' => $r->mes, 'anio' => $r->anio, 'monto' => $r->total_remesa,
            'casa' => $r->vivienda->nro_casa, 'propietario' => $r->vivienda->propietario->nombre ?? 'S/N',
            'concepto' => 'Expensas/Remesas'
        ]);
    }

    // 2. Agrupar para el PDF
    $morosidadPorMes = $todoPendiente->groupBy(['anio', 'mes']);
    $totalDeudaGlobal = $todoPendiente->sum('monto');

    // 3. Generar PDF
    $pdf = Pdf::loadView('admin.reportes.morosidad_pdf', compact('morosidadPorMes', 'totalDeudaGlobal'));

    return $pdf->setPaper('letter', 'portrait')->download('Reporte_Morosidad_Cronologico.pdf');
}
}