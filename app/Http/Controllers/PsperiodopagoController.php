<?php

namespace App\Http\Controllers;

use App\Psperiodopago;

use Illuminate\Http\Request;

use DB;

class PsperiodopagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsperiodopago()
    {


        try {

            return response()->json(Psperiodopago::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsperiodopago($id)
    {


        try {

            return response()->json(Psperiodopago::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPsperiodopago() {
			
			
			try {


				$qry = "select id as value, nomperiodopago as label from psperiodopago";
				$data = DB::select($qry);				
               return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }
		
		
	}

    public function create(Request $request)
    {


        try {

            $data = Psperiodopago::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Psperiodopago::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psperiodopago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

  
	
	
}
