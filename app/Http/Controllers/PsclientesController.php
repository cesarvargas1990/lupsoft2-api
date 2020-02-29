<?php

namespace App\Http\Controllers;

use App\Psclientes;

use Illuminate\Http\Request;

 
use DB;

class PsclientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsclientes($nitempresa)
    {


        try {
            $qry = "SELECT 
                        id,
                        nomcliente,
                        codtipdocid,
                        numdocumento,
                        ciudad,
                        telefijo,
                        celular,
                        direcasa,
                        diretrabajo,
                        ubicasa,
                        ubictrabajo,
                        nitempresa,
                        ref1,
                        ref2,
                        id_cobrador,
                        email,
                        perfil_facebook,
                        fch_expdocumento , 
                        fch_nacimiento , 
                        id_user,
                        created_at,
                        updated_at
                    FROM psclientes
                    WHERE nitempresa = :nitempresa";

            $binds = [
                'nitempresa' => $nitempresa
            ];
            
            $data = DB::select($qry,$binds);
            //dd($data);
            return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsclientes($id)
    {


        try {

            $qry = "SELECT 
                        id,
                        nomcliente,
                        codtipdocid,
                        numdocumento,
                        ciudad,
                        telefijo,
                        celular,
                        direcasa,
                        diretrabajo,
                        ubicasa,
                        ubictrabajo,
                        nitempresa,
                        ref1,
                        ref2,
                        id_cobrador,
                        email,
                        perfil_facebook,
                        fch_expdocumento fch_expdocumento, 
                        fch_nacimiento  fch_nacimiento, 
                        id_user,
                        created_at,
                        updated_at
                    FROM psclientes
                    WHERE nitempresa = :nitempresa";

            $binds = [
                'id' => $id
            ];
            
            $data = DB::select($qry,$binds);

            return response()->json($data);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPsclientes($nitempresa) {
			
			
			try {


				$qry = "select id as value, nomcliente as label from psclientes where nitempresa = :nitempresa";
				$binds = array(
						'nitempresa' => $nitempresa
				);
				$data = DB::select($qry,$binds);				
               return response()->json($data);


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }
		
		
	} 

    public function create(Request $request)
    {

 
        try {

            if ($request->has('fch_expdocumento')) {
                $fch_expdocumento = $request->get('fch_expdocumento');
                $request->request->remove('fch_expdocumento');

                
                $request->request->add(['fch_expdocumento' => substr($fch_expdocumento,0,10) ]);
            }
 
            if ($request->has('fch_nacimiento')) {
                $fch_nacimiento = $request->get('fch_nacimiento');
                $request->request->remove('fch_nacimiento');
                $request->request->add(['fch_nacimiento' => substr($fch_nacimiento,0,10)  ]);
            }
            $data = Psclientes::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        if ($request->has('fch_expdocumento')) {
            $fch_expdocumento = $request->get('fch_expdocumento');
            $request->request->remove('fch_expdocumento');

            
            $request->request->add(['fch_expdocumento' => substr($fch_expdocumento,0,10) ]);
        }

        if ($request->has('fch_nacimiento')) {
            $fch_nacimiento = $request->get('fch_nacimiento');
            $request->request->remove('fch_nacimiento');
            $request->request->add(['fch_nacimiento' => substr($fch_nacimiento,0,10)  ]);
        }

        try {

            $data = Psclientes::findOrFail($id);
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
