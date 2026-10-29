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
    // 1. Obtiene toda la data para el Reporte General
    private function getDatosReporteGeneral()
    {
        $ingAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('monto_pactado');
        
        $ingReservas = DB::table('reservas')
            ->where('estado_pago', 'Pagado')
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;
        
        $totalIngresos = $ingAgua + $ingMante + $ingRemesas + $ingReservas;
        $totalEgresos = Egreso::sum('monto');
        
        $data = [
            'saldoCaja' => $totalIngresos - $totalEgresos,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'totalPendiente' => CobroAgua::where('estado_pago', 'Pendiente')->sum('total_pagar'),
            'totalUsuarios' => User::where('id_rol', 3)->count(),
        ];

        $detallesAgua = CobroAgua::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Agua '.$i->mes.'/'.$i->anio, 'monto' => $i->total_pagar ];
        });
        
        $detallesMante = CobroMantenimiento::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Mante. '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_fijo ];
        });

        $detallesRemesas = CobroRemesa::with('vivienda')
            ->selectRaw('id_vivienda, mes, anio, MAX(fecha_pago) as fecha_pago, SUM(monto_pactado) as monto_pactado')
            ->where('estado_pago', 'Pagado')
            ->groupBy('id_vivienda', 'mes', 'anio')
            ->get()->map(function($i){
                return (object)[ 'fecha' => $i->fecha_pago, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Expensas Totales '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_pactado ];
            });

        $detallesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->where('reservas.estado_pago', 'Pagado')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select('reservas.fecha_reserva as fecha', DB::raw("COALESCE(viviendas.nro_casa, 'S/N') as casa"), DB::raw("CONCAT('Alquiler: ', areas_recreativas.nombre_area) as concepto"), 'reservas.costo_pactado as monto')
            ->get()->map(function($r){
                return (object)[ 'fecha' => $r->fecha, 'casa' => $r->casa, 'concepto' => $r->concepto, 'monto' => $r->monto ];
            });

        $data['listaIngresos'] = $detallesAgua->concat($detallesMante)->concat($detallesRemesas)->concat($detallesReservas)->sortByDesc('fecha');
        $data['listaEgresos'] = Egreso::orderBy('fecha_egreso', 'desc')->get();

        return $data;
    }

    // 2. Obtiene toda la data para el Reporte de Morosidad
    private function getDatosMorosidad()
    {
        $pendientesAgua = CobroAgua::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        $pendientesMante = CobroMantenimiento::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        
        $pendientesRemesas = CobroRemesa::with('vivienda.propietario')
            ->selectRaw('id_vivienda, mes, anio, SUM(monto_pactado) as monto_pactado')
            ->where('estado_pago', 'Pendiente')
            ->groupBy('id_vivienda', 'mes', 'anio')
            ->get();

        $pendientesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->where('reservas.estado_pago', 'Pendiente')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select('reservas.*', 'viviendas.nro_casa', 'usuarios.nombre', 'areas_recreativas.nombre_area')
            ->get();

        $todoPendiente = collect();

        foreach($pendientesAgua as $a) $todoPendiente->push((object)['mes' => $a->mes, 'anio' => $a->anio, 'monto' => $a->total_pagar, 'casa' => $a->vivienda->nro_casa, 'propietario' => $a->vivienda->propietario->nombre ?? 'S/N', 'concepto' => 'Agua Potable']);
        foreach($pendientesMante as $m) $todoPendiente->push((object)['mes' => $m->mes, 'anio' => $m->anio, 'monto' => $m->monto_fijo, 'casa' => $m->vivienda->nro_casa, 'propietario' => $m->vivienda->propietario->nombre ?? 'S/N', 'concepto' => 'Mantenimiento Fijo']);
        foreach($pendientesRemesas as $r) $todoPendiente->push((object)['mes' => $r->mes, 'anio' => $r->anio, 'monto' => $r->monto_pactado, 'casa' => $r->vivienda->nro_casa, 'propietario' => $r->vivienda->propietario->nombre ?? 'S/N', 'concepto' => 'Total Expensas/Remesas']);
        foreach($pendientesReservas as $res) $todoPendiente->push((object)['mes' => \Carbon\Carbon::parse($res->fecha_reserva)->month, 'anio' => \Carbon\Carbon::parse($res->fecha_reserva)->year, 'monto' => $res->costo_pactado, 'casa' => $res->nro_casa ?? 'S/N', 'propietario' => $res->nombre, 'concepto' => 'Alquiler: ' . $res->nombre_area]);

        return [
            'morosidadPorMes' => $todoPendiente->groupBy(['anio', 'mes']),
            'totalDeudaGlobal' => $todoPendiente->sum('monto')
        ];
    }

    // 3. Obtiene toda la data para el Reporte de una sola Vivienda
    private function getDatosVivienda($id_vivienda)
    {
        $vivienda = Vivienda::with('propietario')->findOrFail($id_vivienda);
        
        // AQUÍ ESTÁN LOS NUEVOS FILTROS "!=" (Diferente de Anulado)
        $data = [
            'vivienda' => $vivienda,
            'pagosAgua' => CobroAgua::where('id_vivienda', $id_vivienda)
                ->where('estado_pago', '!=', 'Anulado') // <-- Filtro nuevo
                ->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get(),

            'pagosMante' => CobroMantenimiento::where('id_vivienda', $id_vivienda)
                ->where('estado_pago', '!=', 'Anulado') // <-- Filtro nuevo
                ->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get(),

            'historialLecturas' => Lectura::where('id_vivienda', $id_vivienda)
                ->where('estado_lectura', '!=', 'Anulada') // <-- Filtro nuevo (las lecturas usan 'Anulada' con 'A')
                ->orderBy('id_lectura', 'desc')->get(),

            'pagosReservas' => []
        ];

        // Agrupación de remesas sin contar las anuladas
        $data['pagosRemesas'] = CobroRemesa::selectRaw('id_vivienda, mes, anio, estado_pago, SUM(monto_pactado) as monto_pactado')
            ->where('id_vivienda', $id_vivienda)
            ->where('estado_pago', '!=', 'Anulado') // <-- Filtro nuevo
            ->groupBy('id_vivienda', 'mes', 'anio', 'estado_pago')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        if ($vivienda->id_propietario) {
            $data['pagosReservas'] = DB::table('reservas')
                ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
                ->where('reservas.id_usuario', $vivienda->id_propietario)
                ->where('reservas.estado_pago', '!=', 'Anulado') // <-- Filtro nuevo
                ->select('reservas.*', 'areas_recreativas.nombre_area')
                ->orderBy('fecha_reserva', 'desc')
                ->get();
        }

        return $data;
    }
    /* --- 1. REPORTE GENERAL --- */
    public function index()
    {
        $data = $this->getDatosReporteGeneral();
        
        $data['listaIngresos'] = $data['listaIngresos']->take(30);
        $data['listaEgresos'] = $data['listaEgresos']->take(30);

        $vista = view()->exists('admin.reportes.general') ? 'admin.reportes.general' : 'admin.reportes.index';
        return view($vista, $data);
    }

    public function descargarGeneral()
    {
        $data = $this->getDatosReporteGeneral();
        
        $data['listaIngresos'] = $data['listaIngresos']->take(50);
        $data['listaEgresos'] = $data['listaEgresos']->take(50);

        $vistaPdf = view()->exists('admin.reportes.general_pdf') ? 'admin.reportes.general_pdf' : 'admin.reportes.pdf_general';
        $pdf = Pdf::loadView($vistaPdf, $data);
        return $pdf->setPaper('letter', 'portrait')->download('Reporte_General_SIDUMSS.pdf');
    }

    /* --- 2. REPORTE POR VIVIENDA --- */
    public function porVivienda(Request $request)
    {
        $viviendas = Vivienda::with('propietario')->orderByRaw('CAST(nro_casa AS UNSIGNED) ASC')->get();
        $viviendaSeleccionada = null;
        $datos = ['pagosAgua' => [], 'pagosMante' => [], 'pagosRemesas' => [], 'historialLecturas' => [], 'pagosReservas' => []];

        if ($request->filled('nro_casa')) {
            $termino = trim(str_ireplace(['casa', '#'], '', $request->nro_casa));
            $viviendaSeleccionada = Vivienda::with('propietario')->where('nro_casa', $request->nro_casa)->orWhere('nro_casa', $termino)->first();

            if (!$viviendaSeleccionada) {
                return redirect()->route('admin.reportes.vivienda')->with('error', "No se encontró la vivienda '{$request->nro_casa}'.");
            }
        } elseif ($request->filled('id_vivienda')) {
            $viviendaSeleccionada = Vivienda::with('propietario')->find($request->id_vivienda);
        }

        if ($viviendaSeleccionada) {
            $datos = $this->getDatosVivienda($viviendaSeleccionada->id_vivienda);
        }

        return view('admin.reportes.vivienda', array_merge(compact('viviendas', 'viviendaSeleccionada'), $datos));
    }

    public function descargarPorVivienda($id)
    {
        $datos = $this->getDatosVivienda($id);
        
        $datos['historialLecturas'] = $datos['historialLecturas']->take(6);

        $pdf = Pdf::loadView('admin.reportes.vivienda_pdf', $datos);
        return $pdf->setPaper('letter', 'portrait')->download("Reporte_Casa_{$datos['vivienda']->nro_casa}.pdf");
    }

    /* --- 3. REPORTE DE MOROSIDAD --- */
    public function morosidad()
    {
        return view('admin.reportes.morosidad', $this->getDatosMorosidad());
    }

    public function descargarMorosidad()
    {
        $pdf = Pdf::loadView('admin.reportes.morosidad_pdf', $this->getDatosMorosidad());
        return $pdf->setPaper('letter', 'portrait')->download('Reporte_Morosidad_Cronologico.pdf');
    }
}