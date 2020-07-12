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
		'nom_conc_adicional',
		'vlr_capinicial'
    ];

    
    protected $hidden = [

	];



}
