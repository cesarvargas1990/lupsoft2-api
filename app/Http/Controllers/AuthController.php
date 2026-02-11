<?php

namespace App\Http\Controllers;

use App\PsEmpresa;
use App\Psusuperfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

define('VALIDATE_REQUIRE', 'required|string');
class AuthController extends Controller
{
    public function register(Request $request)
    {

        //validate incoming request
        $this->validate($request, [
            'name' => VALIDATE_REQUIRE,
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    public function login(Request $request, PsEmpresa $psempresa, Psusuperfil $psusuperfil)
    {
        $this->validate($request, [
            'email' => VALIDATE_REQUIRE,
            'password' => VALIDATE_REQUIRE,
        ]);
        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token, $psempresa, $psusuperfil);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }
}
