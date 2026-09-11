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

        // 2. Sumar Ingresos Reales
        $ingresosAgua = CobroAgua::where('estado_pago', 'Pagado')->sum('total_pagar');
        $ingresosMante = CobroMantenimiento::where('estado_pago', 'Pagado')->sum('monto_fijo');
        $ingresosRemesas = CobroRemesa::where('estado_pago', 'Pagado')->sum('total_remesa');

        // =========================================================================
        // NUEVO: Sumar las reservas de Áreas Recreativas que ya fueron PAGADAS
        // =========================================================================
        $ingresosReservas = DB::table('reservas')
            ->where('estado_pago', 'Pagado')
            ->sum('costo_pactado') ?? 0;

        // TOTAL GENERAL DE INGRESOS (Agua + Mantenimiento + Expensas + Áreas Recreativas)
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
            'totalUsuarios', 'totalViviendas', 'ingresos', 'ingresosReservas', // <-- Pasamos $ingresosReservas
            'egresos', 'saldo', 'deudasPendientes', 'ultimosEgresos'
        ));
    }
    /**
     * Registrar pago de un Aviso de Agua
     */
    public function registrarPagoAgua($id)
    {
        // 1. Buscamos el cobro de agua y su vivienda relacionada
        $cobro = CobroAgua::with('vivienda')->findOrFail($id);
        
        // 2. Cambiamos el estado del agua a 'Pagado'
        $cobro->update([
            'estado_pago' => 'Pagado',
            'fecha_pago'  => now()
        ]);

        // 3. SINCRONIZACIÓN AUTOMÁTICA CON LAS ÁREAS RECREATIVAS:
        // Buscamos al propietario de esta vivienda
        $idPropietario = $cobro->vivienda->id_propietario ?? null;

        if ($idPropietario) {
            // Cambiamos a 'Pagado' todas las reservas aceptadas de ese mes y año
            DB::table('reservas')
                ->where('id_usuario', $idPropietario)
                ->whereMonth('fecha_reserva', $cobro->mes)
                ->whereYear('fecha_reserva', $cobro->anio)
                ->where('estado_reserva', 'Aceptado')
                ->update([
                    'estado_pago' => 'Pagado' // <-- Cambia a Pagado automáticamente
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
 * Registrar el pago de la planilla de remesas completa para una casa y mes específico
 */
public function registrarPagoRemesas(Request $request)
{
    // Validamos que lleguen los datos necesarios
    $request->validate([
        'id_vivienda' => 'required',
        'mes' => 'required',
        'anio' => 'required'
    ]);

    // Buscamos todas las remesas de esa vivienda en ese periodo y las marcamos como pagadas
    \App\Models\CobroRemesa::where('id_vivienda', $request->id_vivienda)
        ->where('mes', $request->mes)
        ->where('anio', $request->anio)
        ->update([
            'estado_pago' => 'Pagado',
            'fecha_pago' => now()
        ]);

    return redirect()->back()->with('success', 'Planilla de remesas cobrada correctamente.');
}
public function imagenesRecibidas() {
    // 1. Obtenemos el QR actual de la tabla de tarifas
    $config = DB::table('configuracion_tarifas')->where('estado', true)->first();

    // 2. Obtenemos los comprobantes subidos por los vecinos
    $comprobantes = DB::table('comprobantes_pago')
        ->join('usuarios', 'comprobantes_pago.id_usuario', '=', 'usuarios.id_usuario')
        ->select('comprobantes_pago.*', 'usuarios.nombre', 'usuarios.apellido_paterno', 'usuarios.ci')
        ->orderBy('fecha_subida', 'desc')
        ->get();

    // 3. Retornamos la vista que creamos (admin.imagenes.QR)
    return view('admin.imagenes.QR', compact('config', 'comprobantes'));
}

    /**
     * Si el residente pagó por QR y el administrador valida el comprobante
     */
    public function validarComprobante($id)
    {
        $comprobante = DB::table('comprobantes_pago')->where('id_comprobante', $id)->first();

        if (!$comprobante) {
            return back()->with('error', 'Comprobante no encontrado.');
        }

        // Marcar comprobante como validado
        DB::table('comprobantes_pago')
            ->where('id_comprobante', $id)
            ->update(['estado' => 'Validado']);

        // Si el comprobante era de AGUA, también sincronizamos la reserva a 'Pagado'
        if ($comprobante->tipo_pago == 'agua') {
            $cobro = CobroAgua::with('vivienda')->find($comprobante->id_referencia_pago);
            
            if ($cobro) {
                $cobro->update(['estado_pago' => 'Pagado', 'fecha_pago' => now()]);

                if ($cobro->vivienda && $cobro->vivienda->id_propietario) {
                    DB::table('reservas')
                        ->where('id_usuario', $cobro->vivienda->id_propietario)
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

        return back()->with('success', 'Comprobante validado y pago confirmado con éxito.');
    }

public function rechazarComprobante($id)
{
    // Solo marcamos como rechazado (el vecino tendrá que subir otra foto)
    DB::table('comprobantes_pago')->where('id_comprobante', $id)->update(['estado' => 'Rechazado']);
    
    return back()->with('success', 'El comprobante ha sido rechazado.');
}
}