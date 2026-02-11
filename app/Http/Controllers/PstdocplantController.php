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

    public function showAllPstdocplant(Pstdocplant $pstdocplant)
    {
        try {
            return response()->json($pstdocplant::all());
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOnePstdocplant($id, Pstdocplant $pstdocplant)
    {
        try {
            return response()->json($pstdocplant::find($id));
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function ShowPstdocplant(Pstdocplant $pstdocplant)
    {
        try {
            $data = $pstdocplant::select('codtipdocid as value', 'nomtipodocumento as label')->get();
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

    public function create(Request $request, Pstdocplant $pstdocplant)
    {
        try {
            $data = $pstdocplant::create($request->all());
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

    public function update($id, Request $request, Pstdocplant $pstdocplant)
    {
        try {
            $data = $pstdocplant::findOrFail($id);
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

    public function delete($id, Pstdocplant $pstdocplant)
    {
        try {
            $pstdocplant::findOrFail($id)->delete();
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
