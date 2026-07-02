<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class Usuario extends Authenticatable {
    use HasApiTokens, HasUuids;
    // HasApiTokens:
    // - Crear tokens (createToken())
    // - Usar autenticación con Sanctum
    
    protected $table = "usuarios";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['username', 'password', 'google_id'];
    
    public $timestamps = false;

    // Evita que password aparezca cuando Laravel:
    // - convierte el usuario a array
    // - lo devuelve como JSON
    // No afecta a la base de datos, solo a la salida
    protected $hidden = ['password'];
}
