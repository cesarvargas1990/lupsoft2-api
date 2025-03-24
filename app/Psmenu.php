<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Psmenu extends Model
{
    protected $table = 'psmenu';

    protected $fillable = [
        'nombre',
        'ruta',
        'icono',
        'orden',
        'id_mpadre',
        'id_perfil',
        'ind_activo',
        'id_empresa'
    ];

    protected $hidden = [];


    public function padre()
    {
        return $this->belongsTo(Psmenu::class, 'id_mpadre');
    }

    public function hijos()
    {
        return $this->hasMany(Psmenu::class, 'id_mpadre');
    }

    public function perfil()
    {
        return $this->belongsTo(Psperfil::class, 'id_perfil');
    }

    public function empresa()
    {
        return $this->belongsTo(PsEmpresa::class, 'id_empresa');
    }
}