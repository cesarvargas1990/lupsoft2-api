<?php

namespace App;
 

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psperfilaccion extends Model
{


    protected $table = 'psperfilaccion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
	
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

	];

    public function perfil()
    {
        return $this->belongsTo(Psperfil::class, 'id_perfil');
    }

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }




}
