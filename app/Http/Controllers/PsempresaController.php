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



    public function showOnePsempresa(PsEmpresa $psempresa, $nid)
    {
        try {
            $data = $psempresa::where('id', $nid);
            return response()->json($data->first());
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function update($id, Request $request, PsEmpresa $psempresa)
    {
        try {
            $data = $psempresa::findOrFail($id);
            $request->request->add(['nitempresa' => $request->get('nit')]);
            $data->update($request->all());

            return response()->json($data, 200);
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
