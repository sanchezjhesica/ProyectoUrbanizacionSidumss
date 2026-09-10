<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
    'id_rol',
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'ci',
    'telefono',
    'email',
    'password',
    'estado_logico',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function viviendas()
{
    // Ahora es una relación de Uno a Muchos (hasMany)
    // Un usuario tiene muchas viviendas asignadas directamente por su ID
    return $this->hasMany(Vivienda::class, 'id_propietario', 'id_usuario');
}
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
} 