<?php

namespace App\Http\Controllers;

use App\Psformapago;

use Illuminate\Http\Request;
use App\Psperiodopago;
use App\Pstdocplant;
use DB;

class PsformapagoController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsformapago()
    {


        try {

            return response()->json(Psformapago::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsformapago($id)
    {


        try {

            return response()->json(Psformapago::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPsformapago($nitempresa)
    {
        try {
            $data = Psperiodopago::where('nitempresa', $nitempresa)
                ->get(['id as value', 'nomperiodopago as label']);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function create(Request $request)
    {


        try {

            $data = Psformapago::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Psformapago::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psformapago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    

    public function consultaTipoDocPlantilla(Request $request)
    {
        try {
            $nit_empresa = $request->get('nitempresa');

            $data = Pstdocplant::where('nitempresa', $nit_empresa)->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }
	
}
