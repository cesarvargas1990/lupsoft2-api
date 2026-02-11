<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


//this is new
use Tymon\JWTAuth\Contracts\JWTSubject;

class Pstipodocidenti extends Model
{

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $table = 'pstipodocidenti';

    protected $hidden = [];

    public function clientes()
    {
        return $this->hasMany(Psclientes::class, 'id_tipo_docid');
    }
}
