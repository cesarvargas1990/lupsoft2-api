<?php

namespace App\Http\Controllers;

use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\Psfechaspago;


use Illuminate\Http\Request;

use DB;


class PsfechaspagoController extends Controller
{
    use calculadoraCuotasPrestamosTrait;
    public function __construct()
    { 
        $this->middleware('auth');
    }
	 
	// Generic for tables, make repaces Psfechaspago  and Psfechaspago for  your tables  names 

    public function showAllPsfechaspago($id_prestamo)
    {


        try {

            $qry = "select 
            fp.id, 
            pres.id_cliente, 
            pres.id id_prestamo, 
            fecha_pago , 
            format(fp.valor_cuota,2) valcuota,
            format(fp.valor_pagar, 2) valtotal,
            (select p.id_fecha_pago from pspagos p where p.id_fecha_pago = fp.id and ind_abonocapital = 0 ) id_fecha_pago,
            (select p.fecha_realpago from pspagos p where p.id_fecha_pago = fp.id  and ind_abonocapital = 0) fecha_realpago
                      from psfechaspago fp, psprestamos  pres
               where fp.id_prestamo = pres.id 
               
               and pres.id = :id_prestamo";
            $binds = [
                'id_prestamo' => $id_prestamo
            ];
            $data = DB::select($qry,$binds);
            foreach($data as $key => $value) {
                //dd($value->fecha_pago);
                $data[$key]->fecha_pago = $this->SpanishDate(strtotime($value->fecha_pago) ); 
                if ($value->fecha_realpago != "") {
                    $data[$key]->fecha_realpago = $this->SpanishDate(strtotime($value->fecha_realpago) ); 
                } else {
                    $data[$key]->fecha_realpago = 'Pendiente de pago'; 
                }
                
            }
            return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsfechaspago($id)
    {


        try {

            return response()->json(Psfechaspago::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	

    public function create(Request $request)
    {


        try {

            $data = Psfechaspago::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Psfechaspago::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psfechaspago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
}
