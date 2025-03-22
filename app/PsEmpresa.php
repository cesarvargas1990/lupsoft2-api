<?php

namespace App;
 

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;


class PsEmpresa extends Model
{ 
	protected $table = 'psempresa';
    protected $fillable = [
		'nombre',
		'nitempresa',
		'ddirec',
		'ciudad',
		'telefono',
		'pagina',
		'email',
		'vlr_capinicial'
    ];

    
    protected $hidden = [

	];

	public function users()
    {
        return $this->hasMany(User::class, 'id_empresa');
    }

	public function clientes()
    {
        return $this->hasMany(Psclientes::class, 'id_empresa');
    }



}
