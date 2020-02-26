<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;


use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;

class PrestamosController extends Controller
{


    use prestamosTrait;
    use calculadoraCuotasPrestamosTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }




    public function guardarPrestamo(Request $request)
    {


        try {



            $salida = $this->guardarPrestamoFechas($request);
            return response()->json($salida);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function listadoPrestamos (Request $request) {




        try {


            $nit_empresa = $request->get('nitempresa'); 

            $qry = "
            SELECT 
            pre.*,
            cli.*,
            fp.*,
            em.*,
            ide.*,
            pp.*
            FROM 
            psprestamos pre ,
            psclientes cli, 
            psformapago fp, 
            psempresa em, 
            pstipodocidenti ide, 
            psperiodopago pp
            WHERE pre.nitempresa = :nit_empresa
            AND pre.id_forma_pago = fp.id
            AND pre.id_cliente = cli.id
            AND em.nitempresa = pre.nitempresa
            AND  cli.codtipdocid = ide.id
            AND fp.id_periodo_pago = pp.id";
    
            $binds = array (
                'nit_empresa' => $nit_empresa
            );
    
            $data = DB::select($qry,$binds);

        
            return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


     
        dd($data);
        return $data;

    }

    public function test(Request $request)
    {


        try {

            $datos = $this->calcularCuota($request);
            return response()->json($datos);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

}
