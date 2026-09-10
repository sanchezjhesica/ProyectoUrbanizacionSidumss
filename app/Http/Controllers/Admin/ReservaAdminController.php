<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaAdminController extends Controller
{
    public function index()
    {
        // Obtenemos las reservas uniendo las tablas usuarios y areas_recreativas
        $reservas = DB::table('reservas')
            ->join('usuarios', 'reservas.id_usuario', '=', 'usuarios.id_usuario')
            ->join('areas_recreativas', 'reservas.id_area', '=', 'areas_recreativas.id_area')
            ->select(
                'reservas.*',
                'usuarios.nombre',
                'usuarios.apellido_paterno',
                'usuarios.apellido_materno',
                'usuarios.ci',
                'usuarios.telefono',
                'areas_recreativas.nombre_area'
            )
            ->orderBy('reservas.fecha_reserva', 'desc')
            ->get();

        return view('admin.reservas.index', compact('reservas'));
    }

    public function aceptar($id)
    {
        DB::table('reservas')
            ->where('id_reserva', $id)
            ->update(['estado_reserva' => 'Aceptado']);

        return redirect()->back()->with('success', 'La solicitud de reserva ha sido ACEPTADA.');
    }

    public function denegar($id)
    {
        DB::table('reservas')
            ->where('id_reserva', $id)
            ->update(['estado_reserva' => 'Denegado']);

        return redirect()->back()->with('error', 'La solicitud de reserva ha sido DENEGADA.');
    }
}