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

    public function showAllPsperiodopago(Psperiodopago $psperiodopago)
    {

        try {
            return response()->json($psperiodopago::all());
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }


    }

    public function showOnePsperiodopago($id,Psperiodopago $psperiodopago)
    {

        try {
            return response()->json($psperiodopago::find($id));
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }

    }
	
	public function ShowPsperiodopago(Psperiodopago $psperiodopago)
    {
        try {
            $data = $psperiodopago::select('id as value', 'nomperiodopago as label')->get();
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

    public function create(Request $request,Psperiodopago $psperiodopago)
    {


        try {
            $data = $psperiodopago::create($request->all());
            return response()->json($data, 201);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }

    }

    public function update($id,Request $request,Psperiodopago $psperiodopago)
    {
        try {
            $data = $psperiodopago::findOrFail($id);
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

    public function delete($id,Psperiodopago $psperiodopago)
    {
        try {
            $psperiodopago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);
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
