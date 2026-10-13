<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTarifa extends Model
{
    use HasFactory;

    protected $table = 'configuracion_tarifas';
    protected $primaryKey = 'id_config';
    public $timestamps = false; // O true si tienes created_at y updated_at

    protected $fillable = [
        'precio_m3_agua',
        'monto_alcantarillado',
        'monto_mantenimiento_fijo',
        'porcentaje_mora',
        'fecha_aplicacion',
        'estado',
        'qr_pago',
    ];
}