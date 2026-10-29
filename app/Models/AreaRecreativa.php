<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaRecreativa extends Model
{
    protected $table = 'areas_recreativas';

    protected $primaryKey = 'id_area';

    public $timestamps = false;

    protected $fillable = [
        'nombre_area',
        'costo_reserva'
    ];
}