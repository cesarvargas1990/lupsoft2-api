<?php

namespace App;
 

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Psusuperfil extends Model
{


    
    protected $fillable = [
	
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

	];

    protected $table = 'psusperfil';


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function perfil()
    {
        return $this->belongsTo(Psperfil::class, 'id_perfil');
    }

    public function empresa()
    {
        return $this->belongsTo(Psempresa::class, 'id_empresa');
    }


}
