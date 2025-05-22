<?php

namespace App\Http\Controllers;

use App\Psdocadjuntos;
use Illuminate\Http\Request;
use DB;

class PsdocadjuntosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPstdocadjuntos(Psdocadjuntos $psdocadjuntos)
    {
        try {
            return response()->json($psdocadjuntos::all());
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOnePsdocadjuntos($id, Psdocadjuntos $psdocadjuntos)
    {
        try {
            $data = $psdocadjuntos::where('id_cliente', $id)->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }


    public function create(Request $request, Psdocadjuntos $psdocadjuntos)
    {
        try {
            $data = $psdocadjuntos::create($request->all());
            return response()->json($data, 201);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function update($id, Request $request, Psdocadjuntos $psdocadjuntos)
    {
        try {
            $data = $psdocadjuntos::findOrFail($id);
            $data->update($request->all());
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function delete($id, Psdocadjuntos $psdocadjuntos)
    {
        try {
            $psdocadjuntos::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully'), 200);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }
}
