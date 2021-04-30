<?php

namespace App\Http\Controllers;

use App\Pstdocplant;

use Illuminate\Http\Request;

use DB;

class PstdocplantController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth');
    }
	
	// Generic for tables, make repaces Pstdocplant  and pstdocplant for  your tables  names 

    public function showAllPstdocplant()
    {


        try {

            return response()->json(Pstdocplant::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePstdocplant($id)
    {


        try {

            return response()->json(Pstdocplant::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPstdocplant($nitempresa) {
			
			
			try {


				$qry = "select codtipdocid as value, nomtipodocumento as label from pstdocplant";
			
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

            $data = Pstdocplant::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Pstdocplant::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Pstdocplant::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
}
