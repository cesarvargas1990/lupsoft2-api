<?php

namespace App\Http\Controllers;

use App\Pstdocadjuntos;

use Illuminate\Http\Request;

use DB;

class PstdocadjuntosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPstdocadjuntos()
    {


        try {

            return response()->json(Pstdocadjuntos::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePstdocadjuntos($id)
    {


        try {

            return response()->json(Pstdocadjuntos::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPstdocadjuntos($nitempresa) {
			
			
			try {


				$qry = "select id as value, nombre as label from pstdocadjuntos where nitempresa = :nitempresa";
				$binds = array(
						'nitempresa' => $nitempresa
				);
				$data = DB::select($qry,$binds);				
               return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }
		
		
	}

    public function create(Request $request)
    {


        try {

            $data = Pstdocadjuntos::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Pstdocadjuntos::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Pstdocadjuntos::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    
  
	
}
