<?php

namespace App\Http\Controllers;

use App\Pstipodocidenti;

use Illuminate\Http\Request;

use DB;

class PstipodocidentiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPstipodocidenti(Pstipodocidenti $pstipodocidenti)
    {


        try {

            return response()->json($pstipodocidenti::all());


        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);

        }


    }

    public function showOnePstipodocidenti(Pstipodocidenti $pstipodocidenti, $id)
    {


        try {

            return response()->json($pstipodocidenti::find($id));


        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);

        }


    }
	
	

    public function ShowPstipodocidenti(Pstipodocidenti $pstipodocidenti)
    {
        try {
            $data = $pstipodocidenti::select('codtipdocid as value', 'nomtipodocumento as label')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404)->header('Content-Type', 'application/json');
        }
    }       

    public function create(Request $request,Pstipodocidenti $pstipodocidenti)
    {


        try {

            $data = $pstipodocidenti::create($request->all());

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

    public function update($id,Request $request,Pstipodocidenti $pstipodocidenti)
    {


        try {

            $data = $pstipodocidenti::findOrFail($id);
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

    public function delete($id, Psclientes $psclientes)
    {


        try {

            $psclientes::findOrFail($id)->delete();
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
