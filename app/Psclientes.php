<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psclientes extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
	'nomcliente',
	'codtipdocid',
	'numdocumento',
	'ciudad',
	'telefijo',
	'celular',
	'direcasa',
	'diretrabajo',
	'ubicasa',
	'ubictrabajo',
	'nitempresa',
	'ref1',
	'ref2'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];



}
