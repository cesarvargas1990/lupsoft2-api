<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    //this is new

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function perfiles()
    {
        return $this->belongsToMany(Psperfil::class, 'psusperfil', 'id_user', 'id_perfil');
    }

    public function empresa()
    {
        return $this->belongsTo(PsEmpresa::class, 'id_empresa');
    }

    public function prestamosCreados()
    {
        return $this->hasMany(Psprestamos::class, 'id_usu_reg');
    }

    public function pagosRegistrados()
    {
        return $this->hasMany(Pspagos::class, 'id_usureg');
    }

    public function usuariosCreados()
    {
        return $this->hasMany(User::class, 'id_user');
    }

    public function docAdjuntos()
    {
        return $this->hasMany(Psdocadjuntos::class, 'id_usu_cargarch');
    }

    public function tienePerfil($nombrePerfil)
    {
        return $this->perfiles()->where('nombre', $nombrePerfil)->exists();
    }

    public function clientesRegistrados()
    {
        return $this->hasMany(Psclientes::class, 'id_user');
    }

    public function clientesCobrador()
    {
        return $this->hasMany(Psclientes::class, 'id_cobrador');
    }
}
