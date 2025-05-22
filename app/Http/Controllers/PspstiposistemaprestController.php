<?php

namespace App\Http\Controllers;

use App\Pspstiposistemaprest;
use App\Psperiodopago;
use Illuminate\Http\Request;

use DB;

class PspstiposistemaprestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAll(Pspstiposistemaprest $pspstiposistemaprest)
    {


        try {

            return response()->json($pspstiposistemaprest::all());
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOne($id, Pspstiposistemaprest $pspstiposistemaprest)
    {


        try {

            return response()->json($pspstiposistemaprest::find($id));
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function Show($id_empresa, Psperiodopago $psperiodopago)
    {
        try {
            $data = $psperiodopago::where('id_empresa', $id_empresa)
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

    public function create(Request $request, Pspstiposistemaprest $pspstiposistemaprest)
    {
        try {
            $data = $pspstiposistemaprest::create($request->all());
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

    public function update($id, Request $request, Pspstiposistemaprest $pspstiposistemaprest)
    {

        try {
            $data = $pspstiposistemaprest::findOrFail($id);
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

    public function delete($id, Pspstiposistemaprest $pspstiposistemaprest)
    {


        try {

            $pspstiposistemaprest::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully'), 200);
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function list(Pspstiposistemaprest $pspstiposistemaprest)
    {
        try {
            $data = $pspstiposistemaprest::select('codtipsistemap as value', 'nomtipsistemap as label')->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }
}
