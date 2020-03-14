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

            $data = $this->consultaListadoPrestamos($nit_empresa);

        
            return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


     
        //dd($data);
        //return $data;

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

    public function generarVariablesPlantillas($nit_empresa) {
 
        $qry = $this->obtenerQryListadoPrestamos($nit_empresa);
        $qry .= ' limit 1';
        $binds = [
            'nit_empresa' => $nit_empresa
        ];
        $data = DB::select($qry,$binds);

        $array = json_decode(json_encode($data[0]), true);
        $data = [];
        foreach (array_keys($array) as $key=>$value) {
                $data[] = [
                    'title' => $value,
                    'content' => '{'.$value.'}'
                ];
        }
        //dd($array);
        return $data;

    }

    

    public function prestamosCliente (Request $request) {

 
        $nit_empresa = $request->get('nitempresa');
        $id_cliente = $request->get('id_cliente');
        $qry = "SELECT 

        pres.id AS 'Codigo Prestamo',
        FORMAT(pres.valorpres,2) AS 'Valor Prestamo',
        
        (SELECT IFNULL(SUM(valcuota ),0) FROM pspagos
        WHERE id_prestamo = pres.id
        AND id_cliente = cli.id
        AND nitempresa = cli.nitempresa) AS 'Total Abonado' ,
        
        FORMAT(pres.valorpres- (SELECT IFNULL(SUM(valcuota ),0) FROM pspagos
        WHERE id_prestamo = pres.id
        AND id_cliente = cli.id
        AND nitempresa = cli.nitempresa),2)  AS 'Saldo',
        
        pres.numcuotas AS 'Numero Cuotas',
        FORMAT(pres.valseguro,2) AS 'Valor Seguro'
        
        FROM psprestamos pres, psclientes cli
        WHERE pres.id_cliente = cli.id
        AND id_cliente = :id_cliente
        AND cli.nitempresa = :nit_empresa
        ORDER BY pres.id
        ";

        $binds = array(
            'id_cliente' => $id_cliente,
            'nit_empresa' => $nit_empresa
        );

        $data = DB::select($qry,$binds);
        return $data;
        
    }

    public function getPlantillasDocumentosPrestamo (Request $request) {
        try {
 
            $datos = $this->renderTemplate ($request);
            return response()->json($datos);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }
    }
 
    public function prueba (Request $request) {
        $request->request->add(['nit_empresa' => 1]);
        $request->request->add(['id_prestamo' => 1]);
        //$obj = new prestamosTrait();
        $value = $this->renderTemplate( $request);
        dd($value);
    }

    public function eliminarPrestamo ($id_prestamo) {

        if ($id_prestamo != "") {
            
            DB::table('psprestamos')->where('id' ,'=',$id_prestamo)->delete();
            DB::table('psfechaspago')->where('id_prestamo' ,'=',$id_prestamo)->delete();
            DB::table('pspagos')->where('id_prestamo' ,'=',$id_prestamo)->delete();
        }
        
        
        
    }

}
