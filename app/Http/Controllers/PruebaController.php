<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;


use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;

class PruebaController extends Controller
{


    use prestamosTrait;
    use calculadoraCuotasPrestamosTrait;

    public function __construct()
    {
        //$this->middleware('auth');
    }




    public function prueba (Request $request) {
        $request->request->add(['id_prestamo' => 1]);
        $request->request->add(['nitempresa' => '12345']);
        dd($this->renderTemplate($request));

    }

}
