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
	 
    protected $hidden = [

    ];

    public function cliente() {
        return $this->belongsTo(PsClientes::class, 'id_cliente', 'id');
    }
    
    public function fechasPago() {
        return $this->hasMany(PsFechasPago::class, 'id_prestamo', 'id');
    }
    
    public function pagos() {
        return $this->hasMany(PsPagos::class, 'id_prestamo', 'id');
    }

   



}
