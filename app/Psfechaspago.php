<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psfechaspago extends Model
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

    protected $table = 'psfechaspago';

    protected $hidden = [];

    public function prestamo()
    {
        return $this->belongsTo(PsPrestamos::class, 'id_prestamo', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(PsPagos::class, 'id_fecha_pago', 'id');
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
