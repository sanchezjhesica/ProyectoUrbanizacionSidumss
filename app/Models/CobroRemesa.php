<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroRemesa extends Model
{
    protected $table = 'cobros_remesas';
    protected $primaryKey = 'id_cobro_remesa';
    public $timestamps = false;
    protected $fillable = [
        'id_vivienda',
        'id_remesa_config', 
        'mes',
        'anio',
        'monto_pactado',    
        'estado_pago',
        'fecha_pago'
    ];

    public function configuracion()
    {
        return $this->belongsTo(RemesaConfig::class, 'id_remesa_config', 'id_remesa_config');
    }

    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class, 'id_vivienda', 'id_vivienda');
    }
}