<?php

namespace App\Http\Controllers;
use App\Http\Traits\General\menuPrincipalTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\PsEmpresa;
use App\Psusuperfil;

class Controller extends BaseController
{

    use menuPrincipalTrait;
    protected function respondWithToken($token, Psempresa $psempresa, Psusuperfil $psusuperfil)
    {

		$empresa = $psempresa::where('nitempresa',Auth::user()->nitempresa);
        return response()->json([
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => Auth::user(),
            'status' => 'success',
            'menu_usuario' => $this->hacerMenuUsuario( $this->getDatosMenu(Auth::user()->id)),
            'permisos'=> $this->perfilAccion(Auth::user()->id, $psusuperfil),
            'expires_in' => Auth::factory()->getTTL() * 60,
            'time'=> time(),
            'is_admin' => Auth::user()->is_admin,
            'nit_empresa' => Auth::user()->nitempresa,
            'id_empresa' => $empresa->first()->id
        ], 200);
    }
}
