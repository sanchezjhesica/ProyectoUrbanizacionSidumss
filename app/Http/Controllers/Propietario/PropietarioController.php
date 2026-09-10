<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use App\Models\Vivienda;
use App\Models\CobroAgua;
use App\Models\CobroMantenimiento;
use App\Models\CobroRemesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class PropietarioController extends Controller
{
    public function index() {
        $id_usuario = Auth::id(); 
        $viviendas = Vivienda::where('id_propietario', $id_usuario)->get();
        return view('propietario.dashboard', compact('viviendas'));
    }

    public function misAvisos() {
        $id_usuario = Auth::id();
        $misViviendasIds = Vivienda::where('id_propietario', $id_usuario)->pluck('id_vivienda');

        // 1. Avisos de Agua
        $avisosAgua = CobroAgua::whereIn('id_vivienda', $misViviendasIds)
            ->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        // =========================================================================
        // NUEVO: Sumar las reservas ACEPTADAS de cada mes al total de Agua
        // =========================================================================
        foreach ($avisosAgua as $aviso) {
            $montoReservas = DB::table('reservas')
                ->where('id_usuario', $id_usuario)
                ->whereMonth('fecha_reserva', $aviso->mes)
                ->whereYear('fecha_reserva', $aviso->anio)
                ->where('estado_reserva', 'Aceptado') // Solo las aceptadas por el admin
                ->sum('costo_pactado');

            // Atributo temporal 'total_real' que suma: Agua + Alcantarillado + Mora + Reservas
            $aviso->total_real = $aviso->total_pagar + $montoReservas;
        }
            
        // 2. Avisos de Mantenimiento
        $avisosMantenimiento = CobroMantenimiento::whereIn('id_vivienda', $misViviendasIds)
            ->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
            
        // 3. Avisos de Remesas (Expensas)
        $avisosRemesas = CobroRemesa::whereIn('id_vivienda', $misViviendasIds)
            ->select(
                'id_vivienda', 
                'mes', 
                'anio', 
                'estado_pago', 
                'total_remesa as total_mes',
                'id_cobro_remesa as id_referencia' 
            )
            ->orderBy('anio', 'desc')->orderBy('mes', 'desc')
            ->get();

        $config = DB::table('configuracion_tarifas')->where('estado', true)->first();

        return view('propietario.avisos', compact('avisosAgua', 'avisosMantenimiento', 'avisosRemesas', 'config'));
    }

    public function misReservas() {
        $id_usuario = Auth::id();
        $reservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->where('reservas.id_usuario', $id_usuario)
            ->select('reservas.*', 'areas_recreativas.nombre_area') 
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        $areas = DB::table('areas_recreativas')->get();
        return view('propietario.reservas', compact('reservas', 'areas'));
    }

    public function guardarReserva(Request $request) {
        $request->validate([
            'id_area' => 'required',
            'fecha' => 'required|date|after_or_equal:today',
        ]);

        $id_usuario = Auth::id();
        $area = DB::table('areas_recreativas')->where('id_area', $request->id_area)->first();

        // Se registra la reserva con 'estado_reserva' = 'Pendiente' para aprobación del admin
        DB::table('reservas')->insert([
            'id_usuario'     => $id_usuario,
            'id_area'        => $request->id_area,
            'fecha_reserva'  => $request->fecha,
            'costo_pactado'  => $area->costo_reserva,
            'estado_pago'    => 'Pendiente',
            'estado_reserva' => 'Pendiente' // <-- CAMBIO CLAVE
        ]);

        return redirect()->back()->with('success', 'Solicitud de reserva enviada con éxito. Pendiente de aprobación por la administración.');
    }

    // --- MÉTODOS PARA DESCARGAR PDF ---

    public function descargarAvisoAgua($id) {
        $cobro = CobroAgua::with(['vivienda.propietario', 'lectura'])->findOrFail($id);
        
        // CORRECCIÓN CLAVE: Solo sumar reservas que fueron ACEPTADAS por el admin
        $montoReservas = DB::table('reservas')
            ->where('id_usuario', $cobro->vivienda->id_propietario)
            ->whereMonth('fecha_reserva', $cobro->mes)
            ->whereYear('fecha_reserva', $cobro->anio)
            ->where('estado_pago', 'Pendiente')
            ->where('estado_reserva', 'Aceptado') // <-- CAMBIO CLAVE (No cobra denegadas ni pendientes)
            ->sum('costo_pactado');

        $pdf = Pdf::loadView('propietario.recibo_agua', compact('cobro', 'montoReservas'));
        $pdf->setPaper('letter', 'portrait');
        return $pdf->download("Aviso_Agua_{$cobro->mes}_{$cobro->anio}.pdf");
    }

    public function descargarAvisoMantenimiento($id) {
        $cobro = CobroMantenimiento::with(['vivienda.propietario'])->findOrFail($id);
        $pdf = Pdf::loadView('propietario.recibo_mantenimiento', compact('cobro'));
        $pdf->setPaper('letter', 'portrait');
        return $pdf->download("Aviso_Mantenimiento_{$cobro->mes}_{$cobro->anio}.pdf");
    }

    public function descargarAvisoRemesas($id) {
        $cobro = CobroRemesa::with(['vivienda.propietario'])->findOrFail($id);

        $pdf = Pdf::loadView('propietario.recibo_remesas', compact('cobro'));
        $pdf->setPaper('letter', 'portrait');
        return $pdf->download("Aviso_Expensas_{$cobro->mes}_{$cobro->anio}.pdf");
    }

    public function subirComprobante(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|image|max:2048',
            'id_pago' => 'required',
            'tipo_pago' => 'required'
        ]);

        $ruta = $request->file('comprobante')->store('comprobantes', 'public');

        DB::table('comprobantes_pago')->insert([
            'id_usuario' => Auth::id(),
            'id_referencia_pago' => $request->id_pago,
            'tipo_pago' => $request->tipo_pago,
            'ruta_imagen' => $ruta,
            'estado' => 'Pendiente',
            'fecha_subida' => now()
        ]);

        return back()->with('success', '¡Comprobante enviado con éxito! Espere la validación del administrador.');
    }

    public function editPassword() {
        return view('propietario.seguridad');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '¡Contraseña actualizada correctamente!');
    }
}