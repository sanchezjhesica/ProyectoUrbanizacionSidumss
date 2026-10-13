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
    /**
     * Muestra la vista de configuración de tarifas.
     */
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
     * Actualiza las tarifas globales y HEREDA el código QR sin dejar NULL.
     */
    public function updateGlobal(Request $request) 
    {
        $datosValidados = $request->validate([
            'precio_m3_agua'           => ['required', 'numeric', 'gt:0'],
            'monto_alcantarillado'     => ['required', 'numeric', 'gt:0'],
            'monto_mantenimiento_fijo' => ['required', 'numeric', 'gt:0'],
            'porcentaje_mora'          => ['required', 'numeric', 'gte:0'],
        ], [
            'precio_m3_agua.gt'           => 'El precio por m³ de agua debe ser MAYOR a 0.',
            'monto_alcantarillado.gt'     => 'El monto de alcantarillado debe ser MAYOR a 0.',
            'monto_mantenimiento_fijo.gt' => 'El monto de mantenimiento fijo debe ser MAYOR a 0.',
            'porcentaje_mora.gte'         => 'El porcentaje de mora no puede ser negativo.',
        ]);

        DB::beginTransaction();

try {
            // 1. OBTENER EL ÚLTIMO QR SUBIDO EN EL SISTEMA
            $ultimoQR = ConfiguracionTarifa::whereNotNull('qr_pago')
                ->where('qr_pago', '!=', 'sin_qr')
                ->orderBy('id_config', 'desc')
                ->value('qr_pago');

            // Si nunca se ha subido un QR, usamos 'sin_qr'
            $qrVigente = $ultimoQR ?? 'sin_qr';

            // 2. Desactivar la configuración anterior
            ConfiguracionTarifa::where('estado', 1)->update(['estado' => 0]);

            // 3. Crear la nueva tarifa activa con el QR heredado
            ConfiguracionTarifa::create([
                'precio_m3_agua'           => $datosValidados['precio_m3_agua'],
                'monto_alcantarillado'     => $datosValidados['monto_alcantarillado'],
                'monto_mantenimiento_fijo' => $datosValidados['monto_mantenimiento_fijo'],
                'porcentaje_mora'          => $datosValidados['porcentaje_mora'],
                'qr_pago'                  => $qrVigente, // <-- Ahora sí lo guardará porque está en $fillable
                'fecha_aplicacion'         => now(),
                'estado'                   => 1
            ]);

            DB::commit();
            return back()->with('success', '¡Tarifas globales actualizadas y QR heredado con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar las tarifas: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza el precio de una remesa.
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
     * Actualiza el costo de reserva de un área recreativa.
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
     * Actualiza la imagen del QR en la tarifa activa (estado = 1).
     */
    public function updateQR(Request $request)
    {
        $request->validate([
            'qr_pago' => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        // Buscamos la tarifa activa actualmente
        $tarifaActiva = ConfiguracionTarifa::where('estado', 1)->first();

        if (!$tarifaActiva) {
            return back()->with('error', 'No hay una tarifa activa en el sistema. Guarde primero las tarifas globales.');
        }

        try {
            if ($request->hasFile('qr_pago')) {
                // Si la tarifa activa tenía una foto real previa, borrarla del disco
                if (!empty($tarifaActiva->qr_pago) && $tarifaActiva->qr_pago !== 'sin_qr') {
                    Storage::disk('public')->delete($tarifaActiva->qr_pago);
                }

                // Guardar la nueva imagen en public/qrs
                $rutaNuevaImagenQR = $request->file('qr_pago')->store('qrs', 'public');

                // Actualizar la fila activa con la ruta real
                $tarifaActiva->update([
                    'qr_pago' => $rutaNuevaImagenQR
                ]);

                return back()->with('success', '¡Código QR oficial actualizado en la tarifa actual!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al procesar el archivo: ' . $e->getMessage());
        }

        return back()->with('error', 'Ocurrió un error al procesar la imagen del QR.');
    }
}