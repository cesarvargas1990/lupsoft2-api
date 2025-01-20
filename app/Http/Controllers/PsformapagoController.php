<?php

namespace App\Http\Controllers;

use App\Psformapago;

use Illuminate\Http\Request;


use DB;

class PsformapagoController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAllPsformapago()
    {


        try {

            return response()->json(Psformapago::all());


        } catch (\Exception $e) {

            echo response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404)
                ->header('Content-Type', 'application/json');

        }


    }

    public function showOnePsformapago($id)
    {


        try {

            return response()->json(Psformapago::find($id));


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }
	
	public function ShowPsformapago($nitempresa) {
			
			
			try {


				$qry = "select id as value, nomfpago as label from psformapago where nitempresa = :nitempresa";
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

            $data = Psformapago::create($request->all());

            return response()->json($data, 201);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function update($id,Request $request)
    {


        try {

            $data = Psformapago::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);


        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function delete($id)
    {


        try {

            Psformapago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully') , 200);

        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);

        }


    }

    public function consultaFormasPago($nid_empresa){
        

       
 
                $qry = "SELECT fp.id, 
                    fp.id_periodo_pago, 
                    pp.nomperiodopago, 
                    fp.porcint, 
                    fp.ind_solicporcint, 
                    fp.ind_solivalorpres,
                    fp.valorpres,
                    nomfpago,
                    nitempresa ,
                    fp.numcuotas,
                    fp.ind_solinumc
                FROM psformapago fp, psperiodopago pp 
                WHERE fp.id_periodo_pago = pp.id
                AND fp.nitempresa = :nitempresa";
				$binds = array(
						'nitempresa' => $nid_empresa
				);
				$data = DB::select($qry,$binds);				
               return response()->json($data);
        
    }
    
    public function consultaFormaPago($id){
        

       

        $qry = "SELECT fp.id, 
            fp.id_periodo_pago, 
            pp.nomperiodopago, 
            fp.porcint, 
            fp.ind_solicporcint, 
            fp.ind_solivalorpres,
            fp.valorpres,
            nomfpago,
            nitempresa ,
            fp.numcuotas,
            fp.ind_solinumc
        FROM psformapago fp, psperiodopago pp 
        WHERE fp.id_periodo_pago = pp.id
        AND fp.id = :id";
        $binds = array(
                'id' => $id
        );
        $data = DB::select($qry,$binds);				
       return response()->json($data);

}

    public function consultaTipoDocPlantilla(Request $request){
        

            $nit_empresa = $request->get('nitempresa');
                $qry = "SELECT *
                FROM pstdocplant td
                WHERE td.nitempresa = :nitempresa";
				$binds = array(
						'nitempresa' => $nit_empresa
				);
				$data = DB::select($qry,$binds);				
               return response()->json($data);
        
    }
	
}
