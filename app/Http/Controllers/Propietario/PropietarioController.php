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

        $avisosAgua = CobroAgua::with('vivienda')
            ->whereIn('id_vivienda', $misViviendasIds)
            ->where('estado_pago', '!=', 'Anulado')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        foreach ($avisosAgua as $aviso) {
            $montoReservas = DB::table('reservas')
                ->where('id_vivienda', $aviso->id_vivienda)
                ->whereMonth('fecha_reserva', $aviso->mes)
                ->whereYear('fecha_reserva', $aviso->anio)
                ->where('estado_reserva', 'Aceptado')
                ->sum('costo_pactado');

            $aviso->total_real = $aviso->total_pagar + $montoReservas;
        }
            
        $avisosMantenimiento = CobroMantenimiento::with('vivienda')
            ->whereIn('id_vivienda', $misViviendasIds)
            ->where('estado_pago', '!=', 'Anulado')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
            
        $avisosRemesas = CobroRemesa::with('vivienda')
            ->whereIn('id_vivienda', $misViviendasIds)
            ->where('estado_pago', '!=', 'Anulado')
            ->select(
                'id_vivienda', 
                'mes', 
                'anio', 
                'estado_pago', 
                'monto_pactado as total_mes', 
                'id_cobro_remesa as id_referencia' 
            )
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        $config = DB::table('configuracion_tarifas')->where('estado', true)->first();

        return view('propietario.avisos', compact('avisosAgua', 'avisosMantenimiento', 'avisosRemesas', 'config'));
    }

    public function misReservas() {
        $id_usuario = Auth::id();

        $misViviendas = Vivienda::where('id_propietario', $id_usuario)
            ->orderByRaw('CAST(nro_casa AS UNSIGNED) ASC')
            ->get();

        $reservas = DB::table('reservas')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->leftJoin('viviendas', 'reservas.id_vivienda', '=', 'viviendas.id_vivienda')
            ->where('reservas.id_usuario', $id_usuario)
            ->whereMonth('reservas.fecha_reserva', now()->month)
            ->whereYear('reservas.fecha_reserva', now()->year)
            ->select('reservas.*', 'areas_recreativas.nombre_area', 'viviendas.nro_casa') 
            ->orderBy('fecha_reserva', 'desc')
            ->get();

        $areas = DB::table('areas_recreativas')->get();
        return view('propietario.reservas', compact('reservas', 'areas', 'misViviendas'));
    }

    public function guardarReserva(Request $request) 
    {
        $request->validate([
            'id_vivienda' => 'required|exists:viviendas,id_vivienda',
            'id_area'     => 'required|exists:areas_recreativas,id_area',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora_inicio' => 'nullable',
            'hora_fin'    => 'nullable',
        ], [
            'id_vivienda.required' => 'Debe seleccionar a qué vivienda se cargará el cobro de la reserva.',
            'fecha.after_or_equal' => 'No puede reservar en fechas pasadas.',
        ]);

        $id_usuario = Auth::id();
        $area = DB::table('areas_recreativas')->where('id_area', $request->id_area)->first();

        $horaInicio = $request->filled('hora_inicio') ? $request->hora_inicio : '08:00:00';
        $horaFin    = $request->filled('hora_fin')    ? $request->hora_fin    : '23:59:00';

        $consultaCruce = DB::table('reservas')
            ->where('id_area', $request->id_area)
            ->where('fecha_reserva', $request->fecha)
            ->whereIn('estado_reserva', ['Pendiente', 'Aceptado'])
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                      ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                      ->orWhere(function($sub) use ($horaInicio, $horaFin) {
                          $sub->where('hora_inicio', '<=', $horaInicio)
                              ->where('hora_fin', '>=', $horaFin);
                      });
            });

        if ($consultaCruce->exists()) {
            return back()->withInput()->withErrors([
                'cruce' => "El espacio '{$area->nombre_area}' ya tiene una reserva en el horario de {$horaInicio} a {$horaFin} para el día seleccionado."
            ]);
        }

        DB::table('reservas')->insert([
            'id_usuario'     => $id_usuario,
            'id_vivienda'    => $request->id_vivienda,
            'id_area'        => $request->id_area,
            'fecha_reserva'  => $request->fecha,
            'hora_inicio'    => $horaInicio,
            'hora_fin'       => $horaFin,
            'costo_pactado'  => $area->costo_reserva,
            'estado_pago'    => 'Pendiente',
            'estado_reserva' => 'Pendiente'
        ]);

        return redirect()->back()->with('success', 'Solicitud de reserva enviada con éxito. Pendiente de aprobación.');
    }

    public function descargarAvisoAgua($id) {
        $cobro = CobroAgua::with(['vivienda.propietario', 'lectura'])->findOrFail($id);
        
        $montoReservas = DB::table('reservas')
            ->where('id_vivienda', $cobro->id_vivienda)
            ->whereMonth('fecha_reserva', $cobro->mes)
            ->whereYear('fecha_reserva', $cobro->anio)
            ->where('estado_reserva', 'Aceptado')
            ->sum('costo_pactado') ?? 0;

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

    public function updatePassword(Request $request) 
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password'     => [
                'required', 
                'string', 
                'min:8', 
                'confirmed', 
                'different:current_password',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#.\-_]).+$/'
            ],
        ], [
            'current_password.required' => 'Debe ingresar su contraseña actual.',
            'new_password.required'     => 'Debe ingresar la nueva contraseña.',
            'new_password.min'          => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed'    => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.different'    => 'La nueva contraseña debe ser diferente a la contraseña actual.',
            'new_password.regex'        => 'La nueva contraseña debe tener al menos una letra mayúscula, una minúscula, un número y un carácter especial (ej: @, $, !, %, *, ?, &, #, .).',
        ]);

        $user = Auth::user();

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual ingresada es incorrecta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '¡Su contraseña ha sido actualizada con éxito!');
    }
}