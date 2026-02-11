<?php

namespace App\Http\Controllers;

use App\Psclientes;

use App\Psperiodopago;
use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;


use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\Pspstiposistemaprest;

class CuotasController extends Controller
{
    use calculadoraCuotasPrestamosTrait;
    use prestamosTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function calcularCuotas(Request $request, Psperiodopago $psperiodopago, Pspstiposistemaprest $pspstiposistemaprest)
    {
        try {
            $datos = $this->generarTablaAmortizacion($request, $psperiodopago, $pspstiposistemaprest);
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "errorCode" => $e->getCode(),
                "lineError" => $e->getLine(),
                "file" => $e->getFile()
            ], 404);
        }
    }

    public function calcularCuotas2(Request $request, Psperiodopago $psperiodopago, Pspstiposistemaprest $pspstiposistemaprest)
    {
        try {
            $datos = $this->calcularCuota($request, $psperiodopago, $pspstiposistemaprest);
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "errorCode" => $e->getCode(),
                "lineError" => $e->getLine(),
                "file" => $e->getFile()
            ], 404);
        }
    }
}
