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

    public function showAllPstipodocidenti()
    {


        try {

            return response()->json(Pstipodocidenti::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePstipodocidenti($id)
    {


        try {

            return response()->json(Pstipodocidenti::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	

    public function ShowPstipodocidenti()
    {
        try {
            $data = Pstipodocidenti::select('id_tipo_docid as value', 'nomtipodocumento as label')
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

    public function create(Request $request)
    {


        try {

            $data = Pstipodocidenti::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Pstipodocidenti::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psclientes::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
}
