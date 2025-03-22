<?php

namespace App\Http\Controllers;

use App\Psclientes;

use App\Pspagos;
use App\Psquerytabla;
use Illuminate\Http\Request;
use App\Http\Traits\General\prestamosTrait;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\PsEmpresa;
use App\Psperiodopago;
use App\Psprestamos;
use App\Pspstiposistemaprest;
use App\Pstdocplant;

define('APPLICATION_JSON', 'application/json');
class PrestamosController extends Controller
{


    use prestamosTrait;
    use calculadoraCuotasPrestamosTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }




    public function guardarPrestamo(Request $request,Psperiodopago $psperiodopago, Pspstiposistemaprest $pspstiposistemaprest)
    {


        try {

            $salida = $this->guardarPrestamoFechas($request,$psperiodopago,$pspstiposistemaprest);
            return response()->json($salida);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', APPLICATION_JSON);

        }


    }

    public function listadoPrestamos (Request $request) {




        try {


            $id_empresa = $request->get('id_empresa'); 

            $data = $this->consultaListadoPrestamos($id_empresa);

        
            return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', APPLICATION_JSON);

        }



    }

    public function test(Request $request)
    {


        try {

            $datos = $this->calcularCuota($request);
            return response()->json($datos);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', APPLICATION_JSON);

        }


    }

    public function generarVariablesPlantillas($id_empresa) {
 
        $qry = $this->obtenerQryListadoPrestamos($id_empresa);
        $qry .= ' limit 1';
        $binds = [
            'id_empresa' => $id_empresa
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
      $id_empresa = $request->get('id_empresa');
      $id_cliente = $request->get('id_cliente');
  
      // Obtener préstamos del cliente
      $prestamos = Psprestamos::where('id_cliente', $id_cliente)
          ->whereHas('cliente', function ($query) use ($id_empresa) {
              $query->where('id_empresa', $id_empresa);
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
    
    public function getPlantillasDocumentosPrestamo(Request $request,Psquerytabla $psQueryTabla ,Pstdocplant $pstdocplant ) {
    try {
	        
	        $datos = $this->renderTemplate($request, $psQueryTabla ,$pstdocplant);
	        return response()->json($datos);
	    } catch (\Exception $e) {
	        echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
	            ->header('Content-Type', APPLICATION_JSON);
	    }
	}
  


    public function eliminarPrestamo ($id_prestamo) {

        if ($id_prestamo != "") {
            
            DB::table('psprestamos')->where('id' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
            DB::table('psfechaspago')->where('id_prestamo' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
            DB::table('pspagos')->where('id_prestamo' ,'=',$id_prestamo)->update(['ind_estado'=>0]);
        }
        
        
        
    }
  
    public function totalprestado($id_empresa)
    {
        try {  
        $hasPerfil = Auth::user()->perfiles->contains('id', 1);
        if (!$hasPerfil) {
            return "NA";
        }
        $psPrestamosInstance = new Psprestamos();
        return number_format($this->getCapitalPrestado($id_empresa,$psPrestamosInstance), 2);

      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', APPLICATION_JSON);

      }
    }

    public function totalprestadohoy(Request $request, Psprestamos $psprestamos)
    {
        try {

        $hasPerfil = Auth::user()->perfiles->contains('id', 1);

        if (!$hasPerfil) {
            return "NA";
        }
          return number_format($this->getTotalPrestadoHoy($request,$psprestamos), 2);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', APPLICATION_JSON);

      }
    }

    public function totalintereshoy( Request $request, Pspagos $pspagos )
    {
        try {

         return number_format($this->getTotalintereseshoy($request,$pspagos), 2);
         


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', APPLICATION_JSON);

      }
    }

    public function totalinteres( Request $request, Pspagos $pspagos)
    {
        try {

          return number_format($this->getTotalintereses($request,$pspagos), 2);


      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', APPLICATION_JSON);

      }
    }

    public function totalcapital($id_empresa,Request $request, PsEmpresa $ps_empresa, Psprestamos $psprestamos, Pspagos $pspagos, Auth $auth)
    {
        try {
        $hasPerfil = Auth::user()->perfiles->contains('id', 1);
        if (!$hasPerfil) {
            return "NA";
        }
        $request->request->add(['id_empresa'=>$id_empresa]);
        return number_format($this->getCapitalInicial($id_empresa,$ps_empresa) - $this->getValorPrestamos($request,$psprestamos) + $this->getTotalintereses($request,$pspagos,$auth) , 2);
        

      } catch (\Exception $e) {

          echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
              ->header('Content-Type', APPLICATION_JSON);

      }
    }

    public function totales_dashboard(Request $request, PsEmpresa $psEmpresa, Psprestamos $psprestamos,Pspagos $pspagos, Auth $auth) {
      try {
        $id_empresa = $request->get('id_empresa');
        $data = [
        "total_capital_prestado"=>$this->totalcapital($id_empresa,$request,$psEmpresa, $psprestamos, $pspagos, $auth),
        "total_interes"=>$this->totalinteres($request, $pspagos, $auth),
        "total_interes_hoy"=>$this->totalintereshoy($request,$pspagos,$auth),
        "total_prestado_hoy"=>$this->totalprestadohoy($request,$psprestamos),
        "total_prestado"=>$this->totalprestado($id_empresa),
        "ahora"=>Carbon::now()->toDateTimeString()
        ];
        return response()->json($data);
      } catch (\Exception $e) {
        echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
          ->header('Content-Type', APPLICATION_JSON);
      }
    }

}
