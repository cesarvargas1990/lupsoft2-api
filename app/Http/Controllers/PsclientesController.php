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

    public function showAllPsclientes($nitempresa)
    {
        try {
            
            $data = Psclientes::where('nitempresa', $nitempresa)
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

    public function showOnePsclientes($id)
    {
        try {
            $data = Psclientes::find($id);
    
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
    
	
	public function ShowPsclientes($nitempresa) {
        try {
          
            $data = Psclientes::select('id as value', 'nomcliente as label')
                             ->where('nitempresa', $nitempresa)
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
            $request->request->add(['ind_estado'=> 1] );
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

            Psclientes::findOrFail($id)->update(['ind_estado'=>0]);
            Psprestamos::where(['id_cliente'=>$id])->update(['ind_estado'=>0]);
            Pspagos::where(['id_cliente' =>$id])->update(['ind_estado'=>0]);
            Psfechaspago::where(['id_cliente' =>$id])->update(['ind_estado'=>0]);
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	
	
}
