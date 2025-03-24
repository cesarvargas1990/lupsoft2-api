<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pstdocadjuntos extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	protected $table = 'pstdocadjuntos';
	 
    protected $hidden = [

    ];

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }



}
