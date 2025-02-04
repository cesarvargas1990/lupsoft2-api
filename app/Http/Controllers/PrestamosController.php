<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;

use Carbon\Carbon;
use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\PsEmpresa;
use App\Psprestamos;
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

        

    public function prestamosCliente(Request $request) {
      $nit_empresa = $request->get('nitempresa');
      $id_cliente = $request->get('id_cliente');
  
      // Obtener préstamos del cliente
      $prestamos = Psprestamos::where('id_cliente', $id_cliente)
          ->whereHas('cliente', function ($query) use ($nit_empresa) {
              $query->where('nitempresa', $nit_empresa);
          })
          ->where('ind_estado', 1)
          ->with(['fechasPago', 'pagos'])
          ->get()
          ->map(function ($prestamo) {
              $valorTotalPrestamo = $prestamo->fechasPago->sum('valor_pagar');
              $totalAbonado = $prestamo->pagos->where('ind_abonocapital', 0)->sum('valcuota');
              $abonosCapital = $prestamo->pagos->where('ind_abonocapital', 1)->sum('valcuota');
              $saldo = $valorTotalPrestamo - $totalAbonado - $abonosCapital;
  
              return [
                  'Codigo Prestamo' => $prestamo->id,
                  'Numero Cuotas' => $prestamo->numcuotas,
                  'Valor Prestamo' => number_format($prestamo->valorpres, 2),
                  'Valor Total Prestamo' => number_format($valorTotalPrestamo, 2),
                  'Abonos capital' => number_format($abonosCapital, 2),
                  'Total Abonado' => number_format($totalAbonado, 2),
                  'Saldo' => number_format($saldo, 2),
              ];
          });
  
      return response()->json($prestamos);
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
          return number_format($this->getCapitalPrestado($nit_empresa), 2);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalprestadohoy(Request $request)
    {
        try {

          return number_format($this->getTotalPrestadoHoy($request), 2);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalintereshoy( Request $request )
    {
        try {

         return number_format($this->getTotalintereseshoy($request), 2);
         


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalinteres( Request $request )
    {
        try {

          return number_format($this->getTotalintereses($request), 2);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totalcapital($nit_empresa,Request $request)
    {
        try {
          $request->request->add(['nitempresa'=>$nit_empresa]);
          return number_format($this->getCapitalInicial($nit_empresa) - $this->getValorPrestamos($request) + $this->getTotalintereses($request) , 2);
         


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', 'application/json');

      }
    }

    public function totales_dashboard(Request $request) {
      try {
        $nit_empresa = $request->get('nitempresa');
        $data = [
        "total_capital_prestado"=>$this->totalcapital($nit_empresa,$request),
        "total_interes"=>$this->totalinteres($request),
        "total_interes_hoy"=>$this->totalintereshoy($request),
        "total_prestado_hoy"=>$this->totalprestadohoy($request),
        "total_prestado"=>$this->totalprestado($nit_empresa),
        "ahora"=>Carbon::now()->toDateTimeString()
        ];
        return response()->json($data);
      } catch (\Exception $e) {
        echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
          ->header('Content-Type', 'application/json');
      }
    }

}
