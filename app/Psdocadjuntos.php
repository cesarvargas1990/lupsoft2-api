<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psdocadjuntos extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	protected $table = 'psdocadjuntos';
	 
    protected $hidden = [

    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(Pstdocadjuntos::class, 'id_tdocadjunto');
    }

    public function usuarioCargador()
    {
        return $this->belongsTo(User::class, 'id_usu_cargarch');
    }

    public function cliente()
    {
        return $this->belongsTo(Psclientes::class, 'id_cliente');
    }

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }

}
