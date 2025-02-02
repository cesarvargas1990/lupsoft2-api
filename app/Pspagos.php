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
        
        'fecha_pago','id_cliente','id_prestamo','valcuota','fecha_realpago','id_usureg','id_fecha_pago','nitempresa','ind_estado','ind_abonocapital'
        
    ];
  

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	 
	 protected $table = 'pspagos';
	 
    protected $hidden = [

    ];



}
