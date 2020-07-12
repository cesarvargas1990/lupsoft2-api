<?php

namespace App\Http\Controllers;

use App\PsEmpresa;

use Illuminate\Http\Request;

use DB;

class PsempresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function showOnePsempresa($nid)
    {

 
        try {

            $data = PsEmpresa::where('nitempresa',$nid);
            return response()->json($data->first());


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
    public function update($id,Request $request)
    {


        try {

            $data = PsEmpresa::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

	
}
