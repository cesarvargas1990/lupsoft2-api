<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Psperfil extends Model
{
   
    protected $table = 'psperfil';

   
    protected $fillable = [
        'nombre',  // Asumiendo que la columna que identifica el perfil es 'nombre'
    ];

   
    protected $hidden = [
       
    ];

   
    public function users()
    {
        return $this->belongsToMany(User::class, 'psusperfil', 'id_perfil', 'id_user');
    }
}