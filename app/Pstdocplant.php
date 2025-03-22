<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pstdocplant extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        
        'nombre',
        'plantilla_html',
        'id_empresa'
    ];
  

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	 
	 protected $table = 'pstdocplant';
	 
    protected $hidden = [

    ];



}
