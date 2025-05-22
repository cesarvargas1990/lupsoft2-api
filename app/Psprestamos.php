<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psprestamos extends Model
{


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

    protected $table = 'psprestamos';

    protected $hidden = [];

    public function cliente()
    {
        return $this->belongsTo(PsClientes::class, 'id_cliente', 'id');
    }

    public function fechasPago()
    {
        return $this->hasMany(PsFechasPago::class, 'id_prestamo', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(PsPagos::class, 'id_prestamo', 'id');
    }

    public function periodoPago()
    {
        return $this->belongsTo(Psperiodopago::class, 'id_periodo_pago');
    }

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'id_usu_reg');
    }

    public function cobrador()
    {
        return $this->belongsTo(User::class, 'id_cobrador');
    }

    public function tipoSistemaPrestamo()
    {
        return $this->belongsTo(Pspstiposistemaprest::class, 'id_tiposistemaprest');
    }
}
