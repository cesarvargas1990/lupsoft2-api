<?php

namespace App\Http\Controllers;
use App\Http\Traits\General\menuPrincipalTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    use menuPrincipalTrait;
    protected function respondWithToken($token)
    {


        return response()->json([
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => Auth::user(),
            'status' => 'success',
            'menu_usuario' => $this->hacerMenuUsuario( $this->getDatosMenu(Auth::user()->id)),
            'expires_in' => Auth::factory()->getTTL() * 60,
            'time'=> time(),
            'is_admin' => Auth::user()->is_admin,
            'nit_empresa' => Auth::user()->nitempresa
        ], 200);
    }
}
