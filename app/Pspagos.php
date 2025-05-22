<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pspagos extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [

        'fecha_pago',
        'id_cliente',
        'id_prestamo',
        'valcuota',
        'fecha_realpago',
        'id_usureg',
        'id_fecha_pago',
        'id_empresa',
        'ind_estado',
        'ind_abonocapital'

    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $table = 'pspagos';

    protected $hidden = [];


    public function cliente()
    {
        return $this->belongsTo(Psclientes::class, 'id_cliente');
    }

    public function prestamo()
    {
        return $this->belongsTo(Psprestamos::class, 'id_prestamo');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'id_usureg');
    }

    public function fechaPago()
    {
        return $this->belongsTo(Psfechaspago::class, 'id_fecha_pago');
    }

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }
}
