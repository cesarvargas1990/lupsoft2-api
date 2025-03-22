<?php

namespace App;
 

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

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
	'id_empresa',
	'ref1',
	'ref2',
	'id_cobrador',
	'id_user',
	'email',
	'fch_expdocumento',
	'fch_nacimiento',
	'ind_estado'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

	];

	//protected $dateFormat = 'd/m/Y';

	




}
