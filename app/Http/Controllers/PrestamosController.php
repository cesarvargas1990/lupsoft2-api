<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;


use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;

class CuotasController extends Controller
{

    use calculadoraCuotasPrestamosTrait;
    use prestamosTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function calcularCuotas(Request $request)
    {


        try {

            $datos = $this->generarTablaAmortizacion($request);
            return response()->json($datos);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function calcularCuotas2(Request $request)
    {


        try {

            $datos = $this->calcularCuota($request);
            return response()->json($datos);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function guardarPrestamo($request)
    {


        try {

            $salida = $this->guardarPrestamoFechas($request);
            return response()->json($salida);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

}
