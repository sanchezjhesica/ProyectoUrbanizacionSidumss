<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lectura extends Model
{
    protected $table = 'lecturas';
    protected $primaryKey = 'id_lectura';
    public $timestamps = false; // La base de datos usa fecha_registro por defecto

    protected $fillable = [
        'id_vivienda', 
        'id_operador',
        'periodo_mes',    
        'periodo_anio',   
        'lectura_anterior', 
        'lectura_actual',
        'consumo_m3',      // <--- AGREGA ESTO AQUÍ
        'estado_lectura',  // <-- AGREGAR ESTE
        'fecha_anulacion',
    ];

    // Relación con la Vivienda
    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class, 'id_vivienda', 'id_vivienda');
    }

    // Relación con el Operador (Usuario)
    public function operador()
    {
        return $this->belongsTo(User::class, 'id_operador', 'id_usuario');
    }

    /**
     * Relación con el Cobro de Agua.
     * En la nueva BD, una lectura genera específicamente un cobro de agua.
     */
    public function cobroAgua()
    {
        return $this->hasOne(CobroAgua::class, 'id_lectura', 'id_lectura');
    }
}