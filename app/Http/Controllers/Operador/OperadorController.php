<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Vivienda;
use App\Models\Lectura;
use App\Models\CobroAgua;         
use App\Models\CobroMantenimiento; 
use App\Models\CobroRemesa;       
use App\Models\ConfiguracionTarifa; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OperadorController extends Controller
{
    /**
     * PÁGINA 1: DASHBOARD (Estadísticas de trabajo)
     */
    public function index() 
    {
        $idOperador = Auth::id();
        $mesActual = now()->month;
        $anioActual = now()->year;

        // 1. Total de casas en la urbanización
        $totalCasas = Vivienda::count();

        // 2. Lecturas VÁLIDAS del mes actual (Ignora las anuladas en las estadísticas)
        $lecturasMesQuery = Lectura::where('periodo_mes', $mesActual)
            ->where('periodo_anio', $anioActual)
            ->where('estado_lectura', 'Valida'); // <-- SOLO VÁLIDAS

        $lecturasDelMesCount = (clone $lecturasMesQuery)->count();
        $consumoTotalMes     = (clone $lecturasMesQuery)->sum('consumo_m3') ?? 0;
        $promedioConsumo     = (clone $lecturasMesQuery)->avg('consumo_m3') ?? 0;

        // 3. Lecturas válidas registradas HOY
        $lecturasHoyCount = Lectura::where('id_operador', $idOperador)
            ->where('estado_lectura', 'Valida') // <-- SOLO VÁLIDAS
            ->whereDate('fecha_registro', now()->toDateString())
            ->count();

        // 4. Porcentaje de avance de trabajo este mes
        $porcentajeAvance = $totalCasas > 0 ? round(($lecturasDelMesCount / $totalCasas) * 100) : 0;
        $casasFaltantes   = max(0, $totalCasas - $lecturasDelMesCount);

        // 5. Historial reciente del operador
        $lecturas = Lectura::where('id_operador', $idOperador)
            ->with('vivienda.propietario')
            ->orderBy('fecha_registro', 'desc')
            ->take(10)
            ->get();

        return view('operador.dashboard', compact(
            'lecturas', 
            'lecturasHoyCount', 
            'lecturasDelMesCount', 
            'totalCasas', 
            'casasFaltantes',
            'porcentajeAvance',
            'consumoTotalMes', 
            'promedioConsumo'
        ));
    }

    /**
     * PÁGINA 2: FORMULARIO DE NUEVA LECTURA
     */
    public function nuevaLectura() 
    {
        $viviendas = Vivienda::with('propietario')
            ->orderByRaw('CAST(nro_casa AS UNSIGNED) ASC')
            ->get();
        
        // Obtenemos la última lectura real VÁLIDA de cada casa (ignorando las anuladas)
        foreach($viviendas as $v) {
            $ultima = Lectura::where('id_vivienda', $v->id_vivienda)
                             ->where('estado_lectura', 'Valida') // <-- SOLO VÁLIDAS
                             ->orderBy('periodo_anio', 'desc')
                             ->orderBy('periodo_mes', 'desc')
                             ->orderBy('id_lectura', 'desc')
                             ->first();

            $v->ultima_lectura = $ultima ? floatval($ultima->lectura_actual) : 0.00;
        }

        return view('operador.lecturas_form', compact('viviendas'));
    }

    /**
     * ACCIÓN: PROCESAR Y VALIDAR EL GUARDADO
     */
    public function guardarLectura(Request $request) 
    {
        $mesActual = now()->month;
        $anioActual = now()->year;
        $nombreMesActual = Carbon::now()->translatedFormat('F');

        // 1. VALIDACIÓN INICIAL DE CAMPOS
        $request->validate([
            'id_vivienda'    => ['required', 'exists:viviendas,id_vivienda'],
            'lectura_actual' => ['required', 'numeric', 'min:0.01'],
            'mes'            => ['required', 'integer', 'in:' . $mesActual],
            'anio'           => ['required', 'integer', 'in:' . $anioActual],
        ], [
            'id_vivienda.required'    => 'Debe buscar y seleccionar una vivienda válida.',
            'id_vivienda.exists'      => 'La vivienda seleccionada no existe en el sistema.',
            'lectura_actual.required' => 'Debe ingresar la nueva lectura del medidor.',
            'lectura_actual.numeric'  => 'La lectura debe ser un valor numérico.',
            'lectura_actual.min'      => 'La lectura no puede ser cero ni negativa.',
            'mes.in'                  => "Solo se permite registrar lecturas del mes actual ({$nombreMesActual}).",
            'anio.in'                 => "Solo se permite registrar lecturas del año actual ({$anioActual}).",
        ]);

        DB::beginTransaction();

        try {
            // 2. VALIDAR QUE LA VIVIENDA TENGA PROPIETARIO
            $vivienda = Vivienda::with('propietario')->findOrFail($request->id_vivienda);
            if (!$vivienda->id_propietario) {
                throw new \Exception("La Casa #{$vivienda->nro_casa} no tiene un propietario asignado. No se puede emitir el cobro.");
            }

            // =========================================================================
            // 3. VALIDAR DUPLICIDAD: SOLO DE LECTURAS VÁLIDAS
            // (Si fue anulada por el admin, permitirá volver a registrarla)
            // =========================================================================
            $yaRegistrada = Lectura::where('id_vivienda', $request->id_vivienda)
                                   ->where('periodo_mes', $mesActual)
                                   ->where('periodo_anio', $anioActual)
                                   ->where('estado_lectura', 'Valida')
                                   ->exists();
            
            if ($yaRegistrada) {
                throw new \Exception("Ya existe una lectura activa para la Casa #{$vivienda->nro_casa} en este mes ({$nombreMesActual} {$anioActual}).");
            }

            // =========================================================================
            // 4. OBTENER LA ÚLTIMA LECTURA VÁLIDA PARA COMPARAR
            // =========================================================================
            $ultimaLectura = Lectura::where('id_vivienda', $request->id_vivienda)
                                    ->where('estado_lectura', 'Valida')
                                    ->orderBy('periodo_anio', 'desc')
                                    ->orderBy('periodo_mes', 'desc')
                                    ->orderBy('id_lectura', 'desc')
                                    ->first();

            $lecturaAnterior = $ultimaLectura ? floatval($ultimaLectura->lectura_actual) : 0.00;
            $lecturaActual   = floatval($request->lectura_actual);

            if ($lecturaActual <= $lecturaAnterior) {
                throw new \Exception("La nueva lectura ({$lecturaActual} m³) debe ser MAYOR a la lectura anterior ({$lecturaAnterior} m³).");
            }

            $consumoM3 = $lecturaActual - $lecturaAnterior;

            // 5. OBTENER TARIFAS VIGENTES
            $tarifa = ConfiguracionTarifa::where('estado', true)->first();
            if (!$tarifa) {
                throw new \Exception("No hay tarifas configuradas en el sistema.");
            }

            // =========================================================================
            // 6. GUARDAR LA NUEVA LECTURA COMO 'Valida'
            // =========================================================================
            $nuevaLectura = Lectura::create([
                'id_vivienda'      => $request->id_vivienda,
                'id_operador'      => Auth::id(), 
                'periodo_mes'      => $mesActual,
                'periodo_anio'     => $anioActual,
                'lectura_anterior' => $lecturaAnterior,
                'lectura_actual'   => $lecturaActual,
                'consumo_m3'       => $consumoM3,
                'estado_lectura'   => 'Valida',
            ]);

            // =========================================================================
            // 7. GENERAR LOS 3 AVISOS DE COBRO AUTOMÁTICAMENTE
            // =========================================================================

            // --- AVISO 1: AGUA POTABLE ---
            $subtotalAgua = $consumoM3 * $tarifa->precio_m3_agua;
            $tieneDeudaAgua = CobroAgua::where('id_vivienda', $request->id_vivienda)
                                        ->where('estado_pago', 'Pendiente')->exists();
            $montoMora = $tieneDeudaAgua ? ($subtotalAgua * ($tarifa->porcentaje_mora / 100)) : 0;

            CobroAgua::create([
                'id_vivienda'          => $request->id_vivienda,
                'id_lectura'           => $nuevaLectura->id_lectura,
                'mes'                  => $mesActual,
                'anio'                 => $anioActual,
                'subtotal_consumo'     => $subtotalAgua,
                'monto_alcantarillado' => $tarifa->monto_alcantarillado,
                'monto_mora'           => $montoMora,
                'total_pagar'          => $subtotalAgua + $tarifa->monto_alcantarillado + $montoMora,
                'estado_pago'          => 'Pendiente'
            ]);

            // --- AVISO 2: MANTENIMIENTO ---
            CobroMantenimiento::create([
                'id_vivienda' => $request->id_vivienda,
                'mes'         => $mesActual,
                'anio'        => $anioActual,
                'monto_fijo'  => $tarifa->monto_mantenimiento_fijo,
                'estado_pago' => 'Pendiente'
            ]);

            // --- AVISO 3: REMESAS / EXPENSAS ---
            $remesasConfigs = DB::table('remesas_config')->get();

            foreach ($remesasConfigs as $remesa) {
                CobroRemesa::create([
                    'id_vivienda'      => $request->id_vivienda,
                    'id_remesa_config' => $remesa->id_remesa_config,
                    'mes'              => $mesActual,
                    'anio'             => $anioActual,
                    'monto_pactado'    => $remesa->monto_estandar,
                    'estado_pago'      => 'Pendiente'
                ]);
            }

            DB::commit();
            return redirect()->route('operador.lecturas.crear')
                ->with('success', "¡Éxito! Lectura del mes ({$nombreMesActual}) registrada para la Casa #{$vivienda->nro_casa}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}