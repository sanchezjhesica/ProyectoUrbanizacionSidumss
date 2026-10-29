<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    protected $table = 'egresos';
    protected $primaryKey = 'id_egreso';
    public $timestamps = false;

    protected $fillable = [
        'descripcion', 
        'monto', 
        'categoria', 
        'fecha_egreso', 
        'comprobante_nro', 
        'id_admin'
    ];
}