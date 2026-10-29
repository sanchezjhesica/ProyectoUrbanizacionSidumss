<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vivienda;
use App\Models\Egreso;
use App\Models\CobroAgua;
use App\Models\CobroMantenimiento;
use App\Models\CobroRemesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Estadísticas básicas
        $totalUsuarios = User::count();
        $totalViviendas = Vivienda::count();

        // 2. Sumar Ingresos Reales (CORREGIDO: 'monto_pactado')
        $ingresosAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingresosMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingresosRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('monto_pactado');

        // Sumar las reservas de Áreas Recreativas que ya fueron PAGADAS
        $ingresosReservas = DB::table('reservas')
            ->where('estado_pago', 'Pagado')
            ->sum('costo_pactado') ?? 0;

        // TOTAL GENERAL DE INGRESOS
        $ingresos = $ingresosAgua + $ingresosMante + $ingresosRemesas + $ingresosReservas;

        // 3. Egresos y Saldo
        $egresos = Egreso::sum('monto');
        $saldo = $ingresos - $egresos;

        // 4. Deudas Pendientes
        $deudasPendientes = CobroAgua::with('vivienda')
            ->where('estado_pago', 'Pendiente')
            ->orderBy('id_cobro_agua', 'desc')
            ->take(5)->get();

        // 5. Últimos Egresos
        $ultimosEgresos = Egreso::orderBy('fecha_egreso', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsuarios', 'totalViviendas', 'ingresos', 'ingresosReservas', 
            'egresos', 'saldo', 'deudasPendientes', 'ultimosEgresos'
        ));
    }

    /**
     * Registrar pago de un Aviso de Agua
     */
    public function registrarPagoAgua($id)
    {
        $cobro = CobroAgua::with('vivienda')->findOrFail($id);
        
        $cobro->update([
            'estado_pago' => 'Pagado',
            'fecha_pago'  => now()
        ]);

        if ($cobro->id_vivienda) {
            DB::table('reservas')
                ->where('id_vivienda', $cobro->id_vivienda)
                ->whereMonth('fecha_reserva', $cobro->mes)
                ->whereYear('fecha_reserva', $cobro->anio)
                ->where('estado_reserva', 'Aceptado')
                ->update([
                    'estado_pago' => 'Pagado'
                ]);
        }

        return back()->with('success', '¡Cobro de agua y áreas recreativas registrado como PAGADO!');
    }

    /**
     * Registrar pago de Mantenimiento
     */
    public function registrarPagoMantenimiento($id)
    {
        $cobro = CobroMantenimiento::findOrFail($id);
        $cobro->estado_pago = 'Pagado';
        $cobro->fecha_pago = now();
        $cobro->save();

        return redirect()->back()->with('success', 'Pago de Mantenimiento registrado.');
    }

    /**
     * Registrar el pago de la planilla de remesas completa
     */
    public function registrarPagoRemesas(Request $request)
    {
        $request->validate([
            'id_vivienda' => 'required',
            'mes' => 'required',
            'anio' => 'required'
        ]);

        CobroRemesa::where('id_vivienda', $request->id_vivienda)
            ->where('mes', $request->mes)
            ->where('anio', $request->anio)
            ->update([
                'estado_pago' => 'Pagado',
                'fecha_pago' => now()
            ]);

        return redirect()->back()->with('success', 'Planilla de remesas cobrada correctamente.');
    }

    public function imagenesRecibidas() {
        $config = DB::table('configuracion_tarifas')->where('estado', true)->first();

        $comprobantes = DB::table('comprobantes_pago')
            ->join('usuarios', 'comprobantes_pago.id_usuario', '=', 'usuarios.id_usuario')
            ->select('comprobantes_pago.*', 'usuarios.nombre', 'usuarios.apellido_paterno', 'usuarios.ci')
            ->orderBy('fecha_subida', 'desc')
            ->get();

        return view('admin.imagenes.QR', compact('config', 'comprobantes'));
    }

    /**
     * QR y el administrador valida el comprobante
     */
    public function validarComprobante($id)
    {
        $comprobante = DB::table('comprobantes_pago')->where('id_comprobante', $id)->first();

        if (!$comprobante) {
            return back()->with('error', 'Comprobante no encontrado.');
        }

        DB::table('comprobantes_pago')
            ->where('id_comprobante', $id)
            ->update(['estado' => 'Validado']);

        if ($comprobante->tipo_pago == 'agua') {
            $cobro = CobroAgua::with('vivienda')->find($comprobante->id_referencia_pago);
            
            if ($cobro) {
                $cobro->update(['estado_pago' => 'Pagado', 'fecha_pago' => now()]);

                if ($cobro->id_vivienda) {
                    DB::table('reservas')
                        ->where('id_vivienda', $cobro->id_vivienda)
                        ->whereMonth('fecha_reserva', $cobro->mes)
                        ->whereYear('fecha_reserva', $cobro->anio)
                        ->where('estado_reserva', 'Aceptado')
                        ->update(['estado_pago' => 'Pagado']);
                }
            }
        } elseif ($comprobante->tipo_pago == 'mantenimiento') {
            DB::table('cobros_mantenimiento')
                ->where('id_cobro_mantenimiento', $comprobante->id_referencia_pago)
                ->update(['estado_pago' => 'Pagado', 'fecha_pago' => now()]);
        } elseif ($comprobante->tipo_pago == 'remesas') {
            DB::table('cobros_remesas')
                ->where('id_cobro_remesa', $comprobante->id_referencia_pago)
                ->update(['estado_pago' => 'Pagado', 'fecha_pago' => now()]);
        }

        return back()->with('success', '¡Comprobante validado! El cobro y sus reservas asociadas pasaron a PAGADO.');
    }

    public function rechazarComprobante($id)
    {
        DB::table('comprobantes_pago')->where('id_comprobante', $id)->update(['estado' => 'Rechazado']);
        
        return back()->with('success', 'El comprobante ha sido rechazado.');
    }
}