<?php

namespace App\Http\Controllers;

use App\Psclientes;
use App\Psfechaspago;
use App\Pspagos;
use App\Psprestamos;
use Illuminate\Http\Request;


use DB;

class PsclientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsclientes($id_empresa, Psclientes $psclientes)
    {
        try {
            $data = $psclientes::where('id_empresa', $id_empresa)
                ->where('ind_estado', 1)
                ->get();


            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500); // 500 por error de servidor
        }
    }

    public function showOnePsclientes($id, Psclientes $psclientes)
    {
        try {
            $data = $psclientes::find($id);

            if (!$data) {
                return response()->json(['message' => 'Cliente no encontrado'], 404);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    public function ShowPsclientes(Psclientes $psclientes, $id_empresa)
    {
        try {
            $data = $psclientes::select('id as value', 'nomcliente as label')
                ->where('id_empresa', $id_empresa)
                ->where('ind_estado', 1)
                ->get();


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

    public function create(Request $request, Psclientes $psclientes)
    {
        try {
            $this->validate($request, [
                'nomcliente' => 'required|string',
                'id_tipo_docid' => 'required|integer',
                'numdocumento' => 'required|string',
                'id_empresa' => 'required|integer',
                'id_user' => 'required|integer',
                'id_cobrador' => 'required|integer',
                'email' => 'nullable|email',
                'fch_expdocumento' => 'nullable|date',
                'fch_nacimiento' => 'nullable|date',
            ]);

            if ($request->has('fch_expdocumento')) {
                $fch_expdocumento = $request->get('fch_expdocumento');
                $request->request->remove('fch_expdocumento');
                $request->request->add(['fch_expdocumento' => substr($fch_expdocumento, 0, 10)]);
            }
            if ($request->has('fch_nacimiento')) {
                $fch_nacimiento = $request->get('fch_nacimiento');
                $request->request->remove('fch_nacimiento');
                $request->request->add(['fch_nacimiento' => substr($fch_nacimiento, 0, 10)]);
            }
            $request->request->add(['ind_estado' => 1]);
            $data = $psclientes::create($request->all());
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

    public function update($id, Request $request, Psclientes $psclientes)
    {
        $this->validate($request, [
            'id_tipo_docid' => 'sometimes|integer',
            'id_empresa' => 'sometimes|integer',
            'id_user' => 'sometimes|integer',
            'id_cobrador' => 'sometimes|integer',
            'email' => 'sometimes|nullable|email',
            'fch_expdocumento' => 'sometimes|nullable|date',
            'fch_nacimiento' => 'sometimes|nullable|date',
        ]);

        if ($request->has('fch_expdocumento')) {
            $fch_expdocumento = $request->get('fch_expdocumento');
            $request->request->remove('fch_expdocumento');
            $request->request->add(['fch_expdocumento' => substr($fch_expdocumento, 0, 10)]);
        }
        if ($request->has('fch_nacimiento')) {
            $fch_nacimiento = $request->get('fch_nacimiento');
            $request->request->remove('fch_nacimiento');
            $request->request->add(['fch_nacimiento' => substr($fch_nacimiento, 0, 10)]);
        }
        try {
            $data = $psclientes::findOrFail($id);
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

    public function delete($id, Psclientes $psclientes, Psprestamos $psprestamos, Pspagos $pspagos, Psfechaspago $psfechaspago)
    {
        try {
            $psclientes::findOrFail($id)->update(['ind_estado' => 0]);
            $psprestamos::where(['id_cliente' => $id])->update(['ind_estado' => 0]);
            $pspagos::where(['id_cliente' => $id])->update(['ind_estado' => 0]);
            $psfechaspago::where(['id_cliente' => $id])->update(['ind_estado' => 0]);
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
