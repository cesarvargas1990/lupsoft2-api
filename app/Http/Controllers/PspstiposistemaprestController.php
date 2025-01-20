<?php

namespace App\Http\Controllers;

use App\Pspstiposistemaprest;

use Illuminate\Http\Request;

use DB;

class PspstiposistemaprestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAll()
    {


        try {

            return response()->json(Pspstiposistemaprest::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOne($id)
    {


        try {

            return response()->json(Pspstiposistemaprest::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function Show($nitempresa) {
			
			
			try {


				$qry = "select id as value, nomperiodopago as label from psperiodopago where nitempresa = :nitempresa";
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

            $data = Pspstiposistemaprest::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Pspstiposistemaprest::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Pspstiposistemaprest::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function list() {
			
			 
        try {


            $qry = "select codtipsistemap as value, nomtipsistemap as label from pstiposistemaprest";
           
            $data = DB::select($qry);				
           return response()->json($data);


    } catch (\Exception $e) {

        echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
            ->header('Content-Type', 'application/json');

    }
    
    
}


	
}
