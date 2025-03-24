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

	public function prestamos()
	{
		return $this->hasMany(Psprestamos::class, 'id_empresa');
	}

	public function fechaspago()
	{
		return $this->hasMany(Psfechaspago::class, 'id_empresa');
	}

	public function pagos()
	{
		return $this->hasMany(Pspagos::class, 'id_empresa');
	}

	public function docadjuntos()
	{
		return $this->hasMany(Psdocadjuntos::class, 'id_empresa');
	}

	public function periodospago()
	{
		return $this->hasMany(Psperiodopago::class, 'id_empresa');
	}

	public function querytablas()
	{
		return $this->hasMany(Psquerytabla::class, 'id_empresa');
	}

	public function perfiles()
	{
		return $this->hasMany(Psperfil::class, 'id_empresa');
	}

	public function userperfiles()
	{
		return $this->hasMany(Psusuperfil::class, 'id_empresa');
	}

	public function menus()
	{
		return $this->hasMany(Psmenu::class, 'id_empresa');
	}

	public function docplantillas()
	{
		return $this->hasMany(Pstdocplant::class, 'id_empresa');
	}



}
