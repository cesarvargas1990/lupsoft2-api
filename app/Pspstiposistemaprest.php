<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pspstiposistemaprest extends Model
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

    protected $table = 'pstiposistemaprest';


    protected $fillable = ['formula_calculo'];
    protected $hidden = [];

    public function prestamos()
    {
        return $this->hasMany(Psprestamos::class, 'id_tipo_sistema_prest');
    }
}
