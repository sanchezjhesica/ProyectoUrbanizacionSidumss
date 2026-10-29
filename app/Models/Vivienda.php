<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vivienda extends Model
{
    protected $table = 'viviendas';
    protected $primaryKey = 'id_vivienda';
    public $timestamps = false;
    
    protected $fillable = [
        'nro_casa', 
        'nro_lote', 
        'calle', 
        'tipo_vivienda', 
        'nro_medidor', 
        'estado_vivienda',
        'id_propietario'
    ];

    public function propietario()
{
    return $this->belongsTo(\App\Models\User::class, 'id_propietario', 'id_usuario');
}
    // --- NUEVAS RELACIONES (Divididas por tipo de cobro) ---

    /**
     * Relación con los cobros de agua.
     * Permite hacer: $vivienda->cobrosAgua
     */
    public function cobrosAgua()
    {
        return $this->hasMany(CobroAgua::class, 'id_vivienda', 'id_vivienda');
    }

    /**
     * Relación con los cobros de mantenimiento.
     * Permite hacer: $vivienda->cobrosMantenimiento
     */
    public function cobrosMantenimiento()
    {
        return $this->hasMany(CobroMantenimiento::class, 'id_vivienda', 'id_vivienda');
    }

    /**
     * Relación con los cobros de remesas (expensas).
     * Permite hacer: $vivienda->cobrosRemesas
     */
    public function cobrosRemesas()
    {
        return $this->hasMany(CobroRemesa::class, 'id_vivienda', 'id_vivienda');
    }

    /**
     * Relación con las lecturas de agua.
     */
    public function lecturas()
    {
        return $this->hasMany(Lectura::class, 'id_vivienda', 'id_vivienda');
    }
}