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
    protected function respondWithToken($token, Psempresa $psempresa, Auth $auth, Psusuperfil $psusuperfil)
    {

		$empresa = $psempresa::where('nitempresa',$auth::user()->nitempresa);
        return response()->json([
            'id' => $auth::user()->id,
            'name' => $auth::user()->name,
            'email' => $auth::user()->email,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $auth::user(),
            'status' => 'success',
            'menu_usuario' => $this->hacerMenuUsuario( $this->getDatosMenu(Auth::user()->id)),
            'permisos'=> $this->perfilAccion($auth::user()->id, $psusuperfil),
            'expires_in' => $auth::factory()->getTTL() * 60,
            'time'=> time(),
            'is_admin' => $auth::user()->is_admin,
            'nit_empresa' => $auth::user()->nitempresa,
            'id_empresa' => $empresa->first()->id
        ], 200);
    }
}
