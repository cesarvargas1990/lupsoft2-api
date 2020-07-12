<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;


use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\Psempresa;
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
        if (count($data)> 0) {
            $array = json_decode(json_encode($data[0]), true);
            $data = [];
            foreach (array_keys($array) as $key=>$value) {
                    $data[] = [
                        'title' => $value,
                        'content' => '{'.$value.'}'
                    ];
            }
            return $data;
        } else {
            return null;
        }
        

    }

     

    public function prestamosCliente (Request $request) {

        
        $nit_empresa = $request->get('nitempresa');
        $empresa = Psempresa::where('nitempresa',$nit_empresa )->first();
        $nom_conc_adicional = $empresa->nom_conc_adicional;
       
        $id_cliente = $request->get('id_cliente');
        $qry = "SELECT 
        pres.id AS 'Codigo Prestamo',
        pres.numcuotas AS 'Numero Cuotas',
        FORMAT(pres.valorpres, 2) AS 'Valor Prestamo',
        FORMAT(pres.valseguro, 2) AS '".$nom_conc_adicional."',
        (SELECT 
          FORMAT(SUM(psf.valor_pagar), 2)  
        FROM
          psfechaspago psf  
        WHERE psf.id_prestamo = pres.id) AS 'Valor Total Prestamo',

        ( select format(sum(ifnull(psp.valcuota,0)),2) from pspagos psp  where psp.id_prestamo = pres.id and psp.ind_abonocapital = 1 ) 'Abonos capital',
        (SELECT 
          FORMAT(IFNULL(SUM(valcuota), 0), 2) 
        FROM
          pspagos pp 
        WHERE pp.id_prestamo = pres.id 
          AND pp.id_cliente = cli.id 
          AND pp.ind_abonocapital = 0
          AND pp.nitempresa = cli.nitempresa) AS 'Total Abonado',
        FORMAT(
          (
            (SELECT 
              SUM(psf.valor_pagar) 
            FROM
              psfechaspago psf 
            WHERE psf.id_prestamo = pres.id) - 
            (SELECT 
              IFNULL(SUM(valcuota), 0) 
            FROM
              pspagos pp 
            WHERE pp.id_prestamo = pres.id 
              AND pp.id_cliente = cli.id 
              AND pp.ind_abonocapital = 0
              AND pp.nitempresa = cli.nitempresa) -

              (SELECT 
              IFNULL(SUM(valcuota), 0) 
            FROM
              pspagos pp 
            WHERE pp.id_prestamo = pres.id 
              AND pp.id_cliente = cli.id 
              AND pp.ind_abonocapital = 1
              AND pp.nitempresa = cli.nitempresa)

          ),
          2
        ) AS 'Saldo' 
      FROM
        psprestamos pres,
        psclientes cli 
      WHERE pres.id_cliente = cli.id 
        AND pres.id_cliente = :id_cliente
        AND cli.nitempresa = :nit_empresa
        AND pres.ind_estado = 1
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
            
            DB::table('psprestamos')->where('id' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
            DB::table('psfechaspago')->where('id_prestamo' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
            DB::table('pspagos')->where('id_prestamo' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
        }
        
        
        
    }
  
    public function totalprestado($nit_empresa)
    {
        try {  

          $datos = number_format($this->getCapitalPrestado($nit_empresa), 2);
          return response()->json($datos);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalprestadohoy(Request $request)
    {
        try {

          $datos = number_format($this->getTotalPrestadoHoy($request), 2);
          return response()->json($datos);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalintereshoy( Request $request )
    {
        try {

          $datos = number_format($this->getTotalintereseshoy($request), 2);
          return response()->json($datos);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalinteres( Request $request )
    {
        try {

          $datos = number_format($this->getTotalintereses($request), 2);
          return response()->json($datos);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalcapital($nit_empresa)
    {
        try {

          $datos = number_format($this->getTotalCapital($nit_empresa), 2);
          return response()->json($datos);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

}
