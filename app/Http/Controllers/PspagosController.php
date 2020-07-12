<?php

namespace App\Http\Controllers;

use App\Pspagos;
use App\Psprestamos;
use App\Psfechaspago;

use Illuminate\Http\Request;

use DB;

class PspagosController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth');
    }
	 
	// Generic for tables, make repaces Pspagos  and Pspagos for  your tables  names 

    public function showAllPspagos()
    {


        try {

            return response()->json(Pspagos::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePspagos($id)
    {


        try {

            return response()->json(Pspagos::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	

    public function create(Request $request)
    {


        try {
          

            if ($request->has('fecha_pago')) {

                $fechasPago = Psfechaspago::where('id',$request->get('id'));
                
                $fechaPago = $fechasPago->first()->fecha_pago;
                $valorCuota = $fechasPago->first()->valor_pagar;
                $valorPago = $request->get('valor_pago') ?? 0;

                if ($valorCuota != "" && $valorCuota <> 0) {

                $now = new \DateTime();
                  


                
                $data = DB::table('pspagos')->insert(
                    [
                        'fecha_pago' => $fechaPago,
                        'id_cliente' => $request->get('id_cliente'),
                        'id_usureg' => $request->get('id_user'),
                        'nitempresa' => $request->get('nitempresa'),
                        'fecha_realpago' => $now->format('Y-m-d') ,
                        'id_prestamo' => $request->get('id_prestamo'),
                        'id_fecha_pago' => $request->get('id'),
                        'valcuota' => $valorCuota ,
                        'ind_estado' => 1,
                        'ind_abonocapital' => 0
                    ]
                );

                if ($valorPago > $valorCuota) {

                    $data = DB::table('pspagos')->insert(
                        [
                            'fecha_pago' => $fechaPago,
                            'id_cliente' => $request->get('id_cliente'),
                            'id_usureg' => $request->get('id_user'),
                            'nitempresa' => $request->get('nitempresa'),
                            'fecha_realpago' => $now->format('Y-m-d') ,
                            'id_prestamo' => $request->get('id_prestamo'),
                            'id_fecha_pago' => $request->get('id'),
                            'valcuota' => $valorPago- $valorCuota ,
                            'ind_estado' => 1,
                            'ind_abonocapital' => 1
                        ]
                    );

                    $qryActualizarPagos = "SELECT id,valor_cuota,valor_pagar,ind_renovar 
                FROM psfechaspago 
                WHERE id_prestamo = :id_prestamo1
                AND id NOT  IN (SELECT id_fecha_pago FROM pspagos WHERE id_prestamo = :id_prestamo2)";

                $bindsActualizarPagos = array(
                    'id_prestamo1' => $request->get('id_prestamo'),
                    'id_prestamo2' => $request->get('id_prestamo')
                );
                
                $prestamo = Psprestamos::find($request->get('id_prestamo'));
                $porcint = $prestamo->first()->porcint;
                $dataActualizarPagos = DB::select($qryActualizarPagos,$bindsActualizarPagos);
                foreach ($dataActualizarPagos as $datoActualizar) {
                    if ($datoActualizar->ind_renovar == 0) {
                        $nuevoValorCuota = ($valorPago - $valorCuota) *  ($porcint / 100);
                        DB::table('psfechaspago') 
                        ->where('id',$datoActualizar->id)
                        ->where('ind_renovar',0)
                        ->update(['valor_cuota'=>$nuevoValorCuota,'valor_pagar' => $nuevoValorCuota  ]);
                    } else {  
                        $nuevoValorCuota = ($valorPago - $valorCuota) *  ($porcint / 100);
                        DB::table('psfechaspago')
                        ->where('id',$datoActualizar->id)
                        ->where('ind_renovar',1)
                        ->update(['valor_cuota'=>$nuevoValorCuota,'valor_pagar' => $datoActualizar->valor_pagar - ($valorPago - $valorCuota)  ]); 
                    }
                    

                }

                
                }

				
				return response()->json($data, 201);

                }

                
               
            }

            

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Pspagos::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Pspagos::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
}
