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

    public function ShowPsformapago($id_empresa)
    {
        try {
            $data = Psperiodopago::get(['id as value', 'nomperiodopago as label']);

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

    public function consultaTipoDocPlantilla(Request $request)
    {
        try {
            $id_empresa = $request->get('id_empresa');

            $data = Pstdocplant::where('id_empresa', $id_empresa)->get();

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
