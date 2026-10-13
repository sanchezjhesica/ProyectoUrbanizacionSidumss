<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionTarifa;
use App\Models\RemesaConfig;
use App\Models\AreaRecreativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TarifaController extends Controller
{
    public function edit()
    {
        $tarifaGlobalActual       = ConfiguracionTarifa::orderBy('id_config', 'desc')->first();
        $listaRemesasConfiguradas = RemesaConfig::all();
        $listaAreasRecreativas    = AreaRecreativa::all();

        return view('admin.tarifas.edit', [
            'tarifa'  => $tarifaGlobalActual,
            'remesas' => $listaRemesasConfiguradas,
            'areas'   => $listaAreasRecreativas,
        ]);
    }

    /**
     * AQUÍ SÍ: Usamos transacciones porque hay 2 operaciones consecutivas
     */
    public function updateGlobal(Request $request) 
    {
        $datosValidados = $request->validate([
            'precio_m3_agua'           => ['required', 'numeric', 'gt:0'],
            'monto_alcantarillado'     => ['required', 'numeric', 'gt:0'],
            'monto_mantenimiento_fijo' => ['required', 'numeric', 'gt:0'],
            'porcentaje_mora'          => ['required', 'numeric', 'gte:0'],
        ], [
            'precio_m3_agua.gt'                 => 'El precio por m³ de agua debe ser MAYOR a 0.',
            'monto_alcantarillado.gt'           => 'El monto de alcantarillado debe ser MAYOR a 0.',
            'monto_mantenimiento_fijo.gt'       => 'El monto de mantenimiento fijo debe ser MAYOR a 0.',
            'porcentaje_mora.gte'               => 'El porcentaje de mora no puede ser negativo.',
        ]);

        // INICIAMOS LA TRANSACCIÓN
        DB::beginTransaction();

        try {
            // Operación 1: Desactivar la configuración anterior
            ConfiguracionTarifa::where('estado', 1)->update(['estado' => 0]);

            // Operación 2: Crear la nueva configuración activa
            ConfiguracionTarifa::create([
                'precio_m3_agua'           => $datosValidados['precio_m3_agua'],
                'monto_alcantarillado'     => $datosValidados['monto_alcantarillado'],
                'monto_mantenimiento_fijo' => $datosValidados['monto_mantenimiento_fijo'],
                'porcentaje_mora'          => $datosValidados['porcentaje_mora'],
                'fecha_aplicacion'         => now(),
                'estado'                   => 1
            ]);

            // Ambas salieron bien: Guardamos los cambios definitivamente
            DB::commit();

            return back()->with('success', '¡Tarifas globales actualizadas con éxito!');

        } catch (\Exception $e) {
            // Si alguna falló: Revertimos todo para no dejar el sistema sin tarifas
            DB::rollBack();
            return back()->with('error', 'Error al actualizar las tarifas: ' . $e->getMessage());
        }
    }

    /**
     * AQUÍ NO ES NECESARIO: Es solo una operación única
     */
    public function updateRemesa(Request $request, $idRemesa) 
    {
        $datosValidados = $request->validate([
            'monto_estandar' => ['required', 'numeric', 'gt:0'],
        ], [
            'monto_estandar.gt' => 'El monto de la expensa debe ser MAYOR a 0.',
        ]);

        $remesaAActualizar = RemesaConfig::findOrFail($idRemesa);
        $remesaAActualizar->update([
            'monto_estandar' => $datosValidados['monto_estandar']
        ]);

        return back()->with('success', 'Precio de ' . $remesaAActualizar->nombre_remesa . ' actualizado con éxito.');
    }

    /**
     * AQUÍ NO ES NECESARIO: Es solo una operación única
     */
    public function updateArea(Request $request, $idAreaRecreativa) 
    {
        $datosValidados = $request->validate([
            'costo_reserva' => ['required', 'numeric', 'gt:0'],
        ], [
            'costo_reserva.gt' => 'El costo de la reserva debe ser MAYOR a 0.',
        ]);

        $areaRecreativaAActualizar = AreaRecreativa::findOrFail($idAreaRecreativa);
        $areaRecreativaAActualizar->update([
            'costo_reserva' => $datosValidados['costo_reserva']
        ]);

        return back()->with('success', 'Precio de ' . $areaRecreativaAActualizar->nombre_area . ' actualizado con éxito.');
    }

    /**
     * En updateQR: Un try-catch es ideal para proteger la subida de archivos
     */
    public function updateQR(Request $request)
    {
        $request->validate([
            'qr_pago' => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        $configuracionTarifaActiva = DB::table('configuracion_tarifas')->where('estado', true)->first();

        if (!$configuracionTarifaActiva) {
            return back()->with('error', 'No hay una configuración activa. Primero guarde las tarifas globales.');
        }

        try {
            if ($request->hasFile('qr_pago')) {
                // Eliminar el archivo viejo si existe
                if (!empty($configuracionTarifaActiva->qr_pago)) {
                    Storage::disk('public')->delete($configuracionTarifaActiva->qr_pago);
                }

                // Guardar el nuevo archivo
                $rutaNuevaImagenQR = $request->file('qr_pago')->store('qrs', 'public');

                // Actualizar BD
                DB::table('configuracion_tarifas')
                    ->where('id_config', $configuracionTarifaActiva->id_config)
                    ->update(['qr_pago' => $rutaNuevaImagenQR]);

                return back()->with('success', '¡Código QR oficial actualizado con éxito!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al procesar el archivo: ' . $e->getMessage());
        }

        return back()->with('error', 'Ocurrió un error al procesar la imagen del QR.');
    }
}