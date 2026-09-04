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
        // 1. Buscamos todas las viviendas que tengan deudas pendientes en cualquiera de los 3 servicios
        $viviendasMorosas = Vivienda::with('propietario')
            ->whereHas('cobrosAgua', function($query) { 
                $query->where('estado_pago', 'Pendiente'); 
            })
            ->orWhereHas('cobrosMantenimiento', function($query) { 
                $query->where('estado_pago', 'Pendiente'); 
            })
            ->orWhereHas('cobrosRemesas', function($query) { 
                $query->where('estado_pago', 'Pendiente'); 
            })
            ->get();

        // 2. Recorremos cada vivienda para calcular sus deudas por separado
        foreach ($viviendasMorosas as $vivienda) {
            
            // Sumamos cuánto debe por consumo de Agua
            $deudaAgua = CobroAgua::where('id_vivienda', $vivienda->id_vivienda)
                ->where('estado_pago', 'Pendiente')
                ->sum('total_pagar');

            // Sumamos cuánto debe por cuota de Mantenimiento
            $deudaMantenimiento = CobroMantenimiento::where('id_vivienda', $vivienda->id_vivienda)
                ->where('estado_pago', 'Pendiente')
                ->sum('monto_fijo');

            // Sumamos cuánto debe por Remesas (Seguridad, Jardín, etc.)
            $deudaRemesas = CobroRemesa::where('id_vivienda', $vivienda->id_vivienda)
                ->where('estado_pago', 'Pendiente')
                ->sum('total_remesa');

            // Guardamos el total general sumando los tres servicios
            $vivienda->total_deuda = $deudaAgua + $deudaMantenimiento + $deudaRemesas;

            // Contamos cuántos recibos (papeles) debe en total el propietario
            $cantidadAgua = CobroAgua::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count();
            $cantidadMante = CobroMantenimiento::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count();
            $cantidadRemesas = CobroRemesa::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count();

            $vivienda->cantidad_avisos = $cantidadAgua + $cantidadMante + $cantidadRemesas;
        }

        // 3. Enviamos la lista de morosos a la vista
        return view('admin.reportes.morosidad', [
            'morosos' => $viviendasMorosas
        ]);
    }
public function descargarMorosidad()
{
    // 1. Obtener los mismos datos que la vista web
    $viviendasMorosas = Vivienda::with('propietario')
        ->whereHas('cobrosAgua', function($q) { $q->where('estado_pago', 'Pendiente'); })
        ->orWhereHas('cobrosMantenimiento', function($q) { $q->where('estado_pago', 'Pendiente'); })
        ->orWhereHas('cobrosRemesas', function($q) { $q->where('estado_pago', 'Pendiente'); })
        ->get();

    foreach ($viviendasMorosas as $vivienda) {
        $deudaAgua = CobroAgua::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->sum('total_pagar');
        $deudaMante = CobroMantenimiento::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->sum('monto_fijo');
        $deudaRemesas = CobroRemesa::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->sum('total_remesa');
        
        $vivienda->total_deuda = $deudaAgua + $deudaMante + $deudaRemesas;
        $vivienda->cantidad_avisos = CobroAgua::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count() +
                                     CobroMantenimiento::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count() +
                                     CobroRemesa::where('id_vivienda', $vivienda->id_vivienda)->where('estado_pago', 'Pendiente')->count();
    }

    // 2. Cargar vista para PDF
    $pdf = Pdf::loadView('admin.reportes.morosidad_pdf', compact('viviendasMorosas'));

    return $pdf->setPaper('letter', 'portrait')->download('Reporte_Morosidad_SIDUMSS.pdf');
}
}