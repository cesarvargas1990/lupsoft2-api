<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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
        'id_tipo_docid',
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
    protected $hidden = [];

    //protected $dateFormat = 'd/m/Y';

    public function empresa()
    {
        return $this->belongsTo(PsEmpresa::class, 'id_empresa');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(Pstipodocidenti::class, 'id_tipo_docid');
    }


    public function cobrador()
    {
        return $this->belongsTo(User::class, 'id_cobrador');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


    public function prestamos()
    {
        return $this->hasMany(Psprestamos::class, 'id_cliente');
    }


    public function pagos()
    {
        return $this->hasMany(Pspagos::class, 'id_cliente');
    }


    public function fechasPago()
    {
        return $this->hasMany(Psfechaspago::class, 'id_cliente');
    }


    public function adjuntos()
    {
        return $this->hasMany(Psdocadjuntos::class, 'id_cliente');
    }
}
