<?php

namespace App\Http\Controllers;

use App\Pspagos;
use App\Psprestamos;

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

                $valorCuota = Psprestamos::find($request->get('id_prestamo'))->valcuota + Psprestamos::find($request->get('id_prestamo'))->valseguro;

                if ($valorCuota != "" && $valorCuota <> 0) {

                    
                
                $fecha_pago = $request->get('fecha_pago');
                $request->request->remove('fecha_pago');

                $date = \DateTime::createFromFormat('d/m/Y', $fecha_pago);
                $now = new \DateTime();
                  

                $data = DB::table('pspagos')->insert(
                    [
                        'fecha_pago' => $date->format('Y-m-d'),
                        'id_cliente' => $request->get('id_cliente'),
                        'id_usureg' => $request->get('id_user'),
                        'nitempresa' => $request->get('nitempresa'),
                        'fecha_realpago' => $now->format('Y-m-d') ,
                        'id_prestamo' => $request->get('id_prestamo'),
                        'id_fecha_pago' => $request->get('id'),
                        'valcuota' => $valorCuota
                    ]
                );

                }

                
               
            }

            //$data = Pspagos::create($request->all());

            return response()->json($data, 201);

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
