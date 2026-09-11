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
     * 1. REPORTE GENERAL (VISTA WEB)
     */
    public function index()
    {
        // Ingresos separados por concepto
        $ingAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('total_remesa');
        
        // Ingresos de Áreas Recreativas por separado
        $ingReservas = DB::table('reservas')
            ->where('estado_pago', 'Pagado')
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;
        
        // Suma total para caja
        $totalIngresos = $ingAgua + $ingMante + $ingRemesas + $ingReservas;
        $totalEgresos = Egreso::sum('monto');
        $saldoCaja = $totalIngresos - $totalEgresos;
        
        $totalPendiente = CobroAgua::where('estado_pago', 'Pendiente')->sum('total_pagar');
        $totalUsuarios = User::where('id_rol', 3)->count();

        // Desglose de ingresos identificando cada concepto
        $detallesAgua = CobroAgua::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Agua '.$i->mes.'/'.$i->anio, 'monto' => $i->total_pagar ];
        });
        $detallesMante = CobroMantenimiento::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Mante. '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_fijo ];
        });
        $detallesRemesas = CobroRemesa::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Remesa '.$i->mes.'/'.$i->anio, 'monto' => $i->total_remesa ];
        });

        // Las reservas entran con su propio concepto
        $detallesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->where('reservas.estado_pago', 'Pagado')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select(
                'reservas.fecha_reserva as fecha',
                DB::raw("COALESCE(viviendas.nro_casa, 'S/N') as casa"),
                DB::raw("CONCAT('Alquiler: ', areas_recreativas.nombre_area) as concepto"),
                'reservas.costo_pactado as monto'
            )
            ->get()->map(function($r){
                return (object)[ 'fecha' => $r->fecha, 'casa' => $r->casa, 'concepto' => $r->concepto, 'monto' => $r->monto ];
            });

        $listaIngresos = $detallesAgua
            ->concat($detallesMante)
            ->concat($detallesRemesas)
            ->concat($detallesReservas)
            ->sortByDesc('fecha')
            ->take(30);

        $listaEgresos = Egreso::orderBy('fecha_egreso', 'desc')->take(30)->get();
        $vista = view()->exists('admin.reportes.general') ? 'admin.reportes.general' : 'admin.reportes.index';

        return view($vista, compact(
            'totalIngresos', 'totalEgresos', 'saldoCaja', 'totalPendiente', 
            'totalUsuarios', 'listaIngresos', 'listaEgresos'
        ));
    }

    /**
     * 2. DESCARGAR REPORTE GENERAL PDF
     */
    public function descargarGeneral()
    {
        $ingAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('total_remesa');
        $ingReservas = DB::table('reservas')
            ->where('estado_pago', 'Pagado')
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

        $totalIngresos = $ingAgua + $ingMante + $ingRemesas + $ingReservas;
        $totalEgresos = Egreso::sum('monto');
        $saldoCaja = $totalIngresos - $totalEgresos;

        $detallesAgua = CobroAgua::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Agua '.$i->mes.'/'.$i->anio, 'monto' => $i->total_pagar ];
        });
        $detallesMante = CobroMantenimiento::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Mante. '.$i->mes.'/'.$i->anio, 'monto' => $i->monto_fijo ];
        });
        $detallesRemesas = CobroRemesa::with('vivienda')->where('estado_pago', 'Pagado')->get()->map(function($i){
            return (object)[ 'fecha' => $i->fecha_pago ?? $i->created_at, 'casa' => $i->vivienda->nro_casa ?? 'S/N', 'concepto' => 'Remesa '.$i->mes.'/'.$i->anio, 'monto' => $i->total_remesa ];
        });
        $detallesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->where('reservas.estado_pago', 'Pagado')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select(
                'reservas.fecha_reserva as fecha',
                DB::raw("COALESCE(viviendas.nro_casa, 'S/N') as casa"),
                DB::raw("CONCAT('Alquiler: ', areas_recreativas.nombre_area) as concepto"),
                'reservas.costo_pactado as monto'
            )
            ->get()->map(function($r){
                return (object)[ 'fecha' => $r->fecha, 'casa' => $r->casa, 'concepto' => $r->concepto, 'monto' => $r->monto ];
            });

        $listaIngresos = $detallesAgua
            ->concat($detallesMante)
            ->concat($detallesRemesas)
            ->concat($detallesReservas)
            ->sortByDesc('fecha')
            ->take(50);

        $listaEgresos = Egreso::orderBy('fecha_egreso', 'desc')->take(50)->get();
        $vistaPdf = view()->exists('admin.reportes.general_pdf') ? 'admin.reportes.general_pdf' : 'admin.reportes.pdf_general';

        $pdf = Pdf::loadView($vistaPdf, compact('totalIngresos', 'totalEgresos', 'saldoCaja', 'listaIngresos', 'listaEgresos'));
        return $pdf->setPaper('letter', 'portrait')->download('Reporte_General_SIDUMSS.pdf');
    }

    /**
     * 3. REPORTE POR VIVIENDA (VISTA WEB - CONCEPTOS SEPARADOS)
     */
    public function porVivienda(Request $request)
    {
        // 1. Obtenemos las casas ordenadas numéricamente
        $viviendas = Vivienda::with('propietario')
            ->orderByRaw('CAST(nro_casa AS UNSIGNED) ASC')
            ->orderBy('nro_casa', 'asc')
            ->get();

        $viviendaSeleccionada = null;
        $pagosAgua = []; $pagosMante = []; $pagosRemesas = []; $historialLecturas = []; $pagosReservas = [];

        // 2. BUSCADOR POR NÚMERO DE CASA O POR SELECTOR
        if ($request->filled('nro_casa')) {
            // Limpia por si escriben "Casa #3", "#3" o simplemente "3"
            $termino = trim(str_ireplace(['casa', '#'], '', $request->nro_casa));

            $viviendaSeleccionada = Vivienda::with('propietario')
                ->where('nro_casa', $request->nro_casa)
                ->orWhere('nro_casa', $termino)
                ->first();

            if (!$viviendaSeleccionada) {
                return redirect()->route('admin.reportes.vivienda')
                    ->with('error', "No se encontró ninguna vivienda con el número '{$request->nro_casa}'.");
            }
        } elseif ($request->filled('id_vivienda')) {
            $viviendaSeleccionada = Vivienda::with('propietario')->find($request->id_vivienda);
        }

        // 3. Cargar datos si se seleccionó o encontró la vivienda
        if ($viviendaSeleccionada) {
            $id = $viviendaSeleccionada->id_vivienda;

            $pagosAgua = CobroAgua::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $pagosMante = CobroMantenimiento::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $pagosRemesas = CobroRemesa::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            $historialLecturas = Lectura::where('id_vivienda', $id)->orderBy('id_lectura', 'desc')->get();

            if ($viviendaSeleccionada->id_propietario) {
                $pagosReservas = DB::table('reservas')
                    ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
                    ->where('reservas.id_usuario', $viviendaSeleccionada->id_propietario)
                    ->select('reservas.*', 'areas_recreativas.nombre_area')
                    ->orderBy('fecha_reserva', 'desc')
                    ->get();
            }
        }

        return view('admin.reportes.vivienda', compact(
            'viviendas', 'viviendaSeleccionada', 'pagosAgua', 
            'pagosMante', 'pagosRemesas', 'historialLecturas', 'pagosReservas'
        ));
    }

    /**
     * 4. DESCARGAR REPORTE POR VIVIENDA PDF (CONCEPTOS SEPARADOS)
     */
    public function descargarPorVivienda($id)
    {
        $vivienda = Vivienda::with('propietario')->findOrFail($id);
        $idPropietario = $vivienda->id_propietario;

        // AGUA: Solo el agua pura (Bs. 122.20)
        $pagosAgua = CobroAgua::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        $pagosMante = CobroMantenimiento::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        $pagosRemesas = CobroRemesa::where('id_vivienda', $id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        $historialLecturas = Lectura::where('id_vivienda', $id)->orderBy('id_lectura', 'desc')->take(6)->get();

        // ÁREAS RECREATIVAS: En su tabla propia
        $pagosReservas = [];
        if ($idPropietario) {
            $pagosReservas = DB::table('reservas')
                ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
                ->where('reservas.id_usuario', $idPropietario)
                ->select('reservas.*', 'areas_recreativas.nombre_area')
                ->orderBy('fecha_reserva', 'desc')
                ->get();
        }

        $pdf = Pdf::loadView('admin.reportes.vivienda_pdf', compact(
            'vivienda', 'pagosAgua', 'pagosMante', 'pagosRemesas', 'historialLecturas', 'pagosReservas'
        ));

        return $pdf->setPaper('letter', 'portrait')->download("Reporte_Casa_{$vivienda->nro_casa}.pdf");
    }

    /**
     * 5. REPORTE DE MOROSIDAD (VISTA WEB - DEUDAS SEPARADAS)
     */
    public function morosidad()
    {
        $pendientesAgua = CobroAgua::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        $pendientesMante = CobroMantenimiento::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        $pendientesRemesas = CobroRemesa::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();

        // Reservas aceptadas que aún no han sido pagadas
        $pendientesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->where('reservas.estado_pago', 'Pendiente')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select('reservas.*', 'viviendas.nro_casa', 'usuarios.nombre', 'areas_recreativas.nombre_area')
            ->get();

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
        // Reservas como concepto independiente de deuda
        foreach($pendientesReservas as $res) {
            $todoPendiente->push((object)[
                'mes' => \Carbon\Carbon::parse($res->fecha_reserva)->month,
                'anio' => \Carbon\Carbon::parse($res->fecha_reserva)->year,
                'monto' => $res->costo_pactado,
                'casa' => $res->nro_casa ?? 'S/N',
                'propietario' => $res->nombre,
                'concepto' => 'Alquiler: ' . $res->nombre_area
            ]);
        }

        $morosidadPorMes = $todoPendiente->groupBy(['anio', function ($item) { return $item->mes; }]);
        $totalDeudaGlobal = $todoPendiente->sum('monto');

        return view('admin.reportes.morosidad', compact('morosidadPorMes', 'totalDeudaGlobal'));
    }

    /**
     * 6. DESCARGAR REPORTE DE MOROSIDAD PDF
     */
    public function descargarMorosidad()
    {
        $pendientesAgua = CobroAgua::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        $pendientesMante = CobroMantenimiento::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();
        $pendientesRemesas = CobroRemesa::with('vivienda.propietario')->where('estado_pago', 'Pendiente')->get();

        $pendientesReservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_usuario', '=', 'viviendas.id_propietario')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->where('reservas.estado_pago', 'Pendiente')
            ->where('reservas.estado_reserva', 'Aceptado')
            ->select('reservas.*', 'viviendas.nro_casa', 'usuarios.nombre', 'areas_recreativas.nombre_area')
            ->get();

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
        foreach($pendientesReservas as $res) {
            $todoPendiente->push((object)[
                'mes' => \Carbon\Carbon::parse($res->fecha_reserva)->month,
                'anio' => \Carbon\Carbon::parse($res->fecha_reserva)->year,
                'monto' => $res->costo_pactado,
                'casa' => $res->nro_casa ?? 'S/N',
                'propietario' => $res->nombre,
                'concepto' => 'Alquiler: ' . $res->nombre_area
            ]);
        }

        $morosidadPorMes = $todoPendiente->groupBy(['anio', 'mes']);
        $totalDeudaGlobal = $todoPendiente->sum('monto');

        $pdf = Pdf::loadView('admin.reportes.morosidad_pdf', compact('morosidadPorMes', 'totalDeudaGlobal'));
        return $pdf->setPaper('letter', 'portrait')->download('Reporte_Morosidad_Cronologico.pdf');
    }
}