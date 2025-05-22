<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psperiodopago extends Model
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

    protected $table = 'psperiodopago';

    protected $hidden = [];

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }

    public function prestamos()
    {
        return $this->hasMany(Psprestamos::class, 'id_periodo_pago');
    }
}
