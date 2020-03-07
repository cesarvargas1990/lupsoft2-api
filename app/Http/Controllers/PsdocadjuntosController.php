<?php

namespace App\Http\Controllers;

use App\Psdocadjuntos;

use Illuminate\Http\Request;

use DB;

class PsdocadjuntosController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth');
    }
	
	// Generic for tables, make repaces Pstdocadjuntos  and Pstdocadjuntos for  your tables  names 

    public function showAllPstdocadjuntos()
    {


        try { 

            return response()->json(Psdocadjuntos::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsdocadjuntos($id)
    {


        try {

             
            $qry = "select * from psdocadjuntos where id_cliente = :id_cliente";
            $binds = array(
                'id_cliente' => $id
            );
            $data = DB::select($qry,$binds);
            return response()->json($data);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	

    public function create(Request $request)
    {


        try {

            $data = Psdocadjuntos::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Psdocadjuntos::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psdocadjuntos::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
}
