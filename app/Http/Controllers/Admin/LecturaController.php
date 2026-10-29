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

        // 1. Cobros de Agua
        $queryAgua = CobroAgua::with(['vivienda.propietario', 'lectura']);
        if($id_vivienda) { $queryAgua->where('id_vivienda', $id_vivienda); }
        $cobrosAgua = $queryAgua->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        foreach ($cobrosAgua as $a) {
            $monto_reservas = DB::table('reservas')
                ->where('id_vivienda', $a->id_vivienda)
                ->whereMonth('fecha_reserva', $a->mes)
                ->whereYear('fecha_reserva', $a->anio)
                ->where('estado_reserva', 'Aceptado')
                ->sum('costo_pactado') ?? 0;

            $a->total_real = $a->total_pagar + $monto_reservas;
            $a->monto_reservas = $monto_reservas;
        }

        // 2. Cobros de Mantenimiento
        $queryMante = CobroMantenimiento::with('vivienda.propietario');
        if($id_vivienda) { $queryMante->where('id_vivienda', $id_vivienda); }
        $cobrosMante = $queryMante->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        // =========================================================================
        // 3. REMESAS AGRUPADAS: 1 SOLA FILA POR PLANILLA (EMITIDAS O ANULADAS)
        // =========================================================================
        $queryRemesas = CobroRemesa::with(['vivienda.propietario']);
        if($id_vivienda) { $queryRemesas->where('id_vivienda', $id_vivienda); }

        $cobrosRemesas = $queryRemesas->select(
                'id_vivienda', 
                'mes', 
                'anio', 
                'estado_pago', 
                DB::raw('MIN(id_cobro_remesa) as id_referencia'),
                DB::raw('SUM(monto_pactado) as total_mes') // Suma 200 + 30 + 0 = 230 en una sola fila
            )
            ->groupBy('id_vivienda', 'mes', 'anio', 'estado_pago')
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

        $monto_reservas = DB::table('reservas')
            ->where('id_vivienda', $vivienda->id_vivienda)
            ->whereMonth('fecha_reserva', $cobroAgua->mes)
            ->whereYear('fecha_reserva', $cobroAgua->anio)
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

        return view('admin.lecturas.recibo', compact('cobroAgua', 'cobroMante'))->with(['monto_wally' => $monto_reservas]);
    }

    public function imprimirAgua($id)
    {
        $cobro = CobroAgua::with(['vivienda.propietario', 'lectura'])->findOrFail($id);
        
        $montoReservas = DB::table('reservas')
            ->where('id_vivienda', $cobro->id_vivienda)
            ->whereMonth('fecha_reserva', $cobro->mes)
            ->whereYear('fecha_reserva', $cobro->anio)
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

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
        
        // Obtenemos los ítems de esa planilla mensual para el desglose del PDF
        $detallesRemesas = CobroRemesa::where('id_vivienda', $cobro->id_vivienda)
            ->where('mes', $cobro->mes)
            ->where('anio', $cobro->anio)
            ->where('estado_pago', $cobro->estado_pago)
            ->get();

        return Pdf::loadView('admin.lecturas.recibo_remesas', compact('detallesRemesas', 'cobro'))
            ->setPaper('letter')->download("Remesas_Casa_{$cobro->vivienda->nro_casa}.pdf");
    }
   /**
     * Anular una lectura errónea y sus cobros generados
     */
    public function anularLectura($id_lectura)
    {
        $lectura = Lectura::with('vivienda')->findOrFail($id_lectura);
        
        // 1. Buscamos los 3 recibos generados en ese mes para esa casa
        $cobroAgua = CobroAgua::where('id_lectura', $id_lectura)->first();
        
        $cobroMante = CobroMantenimiento::where('id_vivienda', $lectura->id_vivienda)
            ->where('mes', $lectura->periodo_mes)
            ->where('anio', $lectura->periodo_anio)
            ->first();

        // Para remesas, verificamos si existe AL MENOS UNA que ya esté pagada en ese mes
        $remesasPagadas = CobroRemesa::where('id_vivienda', $lectura->id_vivienda)
            ->where('mes', $lectura->periodo_mes)
            ->where('anio', $lectura->periodo_anio)
            ->where('estado_pago', 'Pagado')
            ->exists();

        // =========================================================================
        // REGLA DE SEGURIDAD: SI ALGÚN SERVICIO YA FUE PAGADO, SE BLOQUEA LA ANULACIÓN
        // =========================================================================
        if ($cobroAgua && $cobroAgua->estado_pago === 'Pagado') {
            return back()->with('error', 'No se puede anular la lectura porque el recibo de AGUA ya fue pagado.');
        }
        if ($cobroMante && $cobroMante->estado_pago === 'Pagado') {
            return back()->with('error', 'No se puede anular la lectura porque el MANTENIMIENTO ya fue pagado.');
        }
        if ($remesasPagadas) {
            return back()->with('error', 'No se puede anular la lectura porque la planilla de EXPENSAS ya fue pagada.');
        }

        // Si pasó las validaciones, procedemos a anular todo
        DB::beginTransaction();

        try {
            $ahora = now();

            // 1. Marcar la lectura como Anulada
            $lectura->update([
                'estado_lectura'  => 'Anulada',
                'fecha_anulacion' => $ahora
            ]);

            // 2. Anular el aviso de Agua
            if ($cobroAgua) {
                $cobroAgua->update([
                    'estado_pago'     => 'Anulado',
                    'fecha_anulacion' => $ahora
                ]);
            }

            // 3. Anular el aviso de Mantenimiento
            if ($cobroMante) {
                $cobroMante->update([
                    'estado_pago'     => 'Anulado',
                    'fecha_anulacion' => $ahora
                ]);
            }

            // 4. Anular todas las Expensas de ese mes (que están pendientes)
            CobroRemesa::where('id_vivienda', $lectura->id_vivienda)
                ->where('mes', $lectura->periodo_mes)
                ->where('anio', $lectura->periodo_anio)
                ->update([
                    'estado_pago'     => 'Anulado',
                    'fecha_anulacion' => $ahora
                ]);

            DB::commit();

            return back()->with('success', "¡Lectura y cobros del periodo {$lectura->periodo_mes}/{$lectura->periodo_anio} ANULADOS con éxito! El operador ya puede volver a medir la Casa #{$lectura->vivienda->nro_casa}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular la lectura: ' . $e->getMessage());
        }
    }
}