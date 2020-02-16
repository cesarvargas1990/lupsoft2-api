<?php

namespace App\Http\Controllers;

use App\Psclientes;
use Illuminate\Http\Request;


class PsclientesController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsclientes()
    {


		try {

			 return response()->json(Psclientes::all());



		} catch (\Exception $e) {

			echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
					->header('Content-Type', 'application/json');

		}



    }

    public function showOnePsclientes($id)
    {


		try {

			     return response()->json(Psclientes::find($id));



		} catch (\Exception $e) {

			echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
					->header('Content-Type', 'application/json');

		}



    }

    public function create(Request $request)
    {


		try {

			$data = Psclientes::create($request->all());

		return response()->json($data, 201);

		} catch (\Exception $e) {

			echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
					->header('Content-Type', 'application/json');

		}






    }

    public function update($id, Request $request)
    {


		try {

			 $data = Psclientes::findOrFail($id);
        $data->update($request->all());

        return response()->json($data, 200);



		} catch (\Exception $e) {

			echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
					->header('Content-Type', 'application/json');

		}



    }

    public function delete($id)
    {


		try {

		 Psclientes::findOrFail($id)->delete();
        return response('Deleted Successfully', 200);

		} catch (\Exception $e) {

			echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
					->header('Content-Type', 'application/json');

		}



    }
}
