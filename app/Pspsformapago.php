<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psformapago extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */



   

    protected $fillable = [
        'codfpago', 
        'id_periodo_pago', 
        'nomperiodopago', 
        'valseguro',
        'porcint', 
        'ind_solicseguro', 
        'ind_solicporcint', 
        'ind_solivalorpres', 
        'nomfpago',
        'nitempresa',
        'numcuotas',
        'ind_solinumc',
        'valorpres'
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	 
	 protected $table = 'psformapago';
	 
    protected $hidden = [

    ];



}
