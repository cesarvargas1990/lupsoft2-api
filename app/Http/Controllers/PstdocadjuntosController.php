<?php

namespace App\Http\Controllers;

use App\Pstdocadjuntos;
use Illuminate\Http\Request;
use DB;

class PstdocadjuntosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPstdocadjuntos(Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            return response()->json($pstdocadjuntos::all());
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOnePstdocadjuntos($id, Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            return response()->json($pstdocadjuntos::find($id));
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function ShowPstdocadjuntos($id_empresa, Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            $data = $pstdocadjuntos::where('id_empresa', $id_empresa)
                ->select('id as value', 'nombre as label')
                ->get();
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

    public function create(Request $request, Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            $data = Pstdocadjuntos::create($request->all());
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

    public function update($id, Request $request, Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            $data = $pstdocadjuntos::findOrFail($id);
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

    public function delete($i, Pstdocadjuntos $pstdocadjuntos)
    {
        try {
            $pstdocadjuntos::findOrFail($id)->delete();
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
}
