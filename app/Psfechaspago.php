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
	 
    protected $hidden = [

    ];



}
