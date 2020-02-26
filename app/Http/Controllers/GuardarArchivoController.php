<?php

namespace App\Http\Controllers;

use App\Psclientes;
use App\Psformapago;

use Illuminate\Http\Request;

use DB;

use App\Pstdocadjuntos;

class GuardarArchivoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function guardarArchivoAdjunto(Request $request)
    {
                
        
        $tdoc = $request->get('id_tdocadjunto');
        $nitempresa = $request->get('nitempresa');
        $id_cliente = $request->get('id_cliente');
        $id_usuario = $request->get('id_usuario');
        $customFilename = $request->get('filename');
        $fileExt = $request->get('fileExt');
        $path = $request->get('path');

        if ($request->has('image')) {

            
            $destination_path = $path;

            if ($tdoc) {
                $archivoAdjunto = $tdoc . '-' . time() . '.' . $fileExt;
            } else {
                $archivoAdjunto = $customFilename;
            }
            
            
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->get('image')));
            
            $file = $destination_path . $archivoAdjunto;
            if (file_put_contents($file, $data)) {

                if (!$tdoc == "") {

                    DB::table('psdocadjuntos')->insertGetId([
                        'rutaadjunto' => $file,
                        'id_tdocadjunto' => $tdoc,
                        'nombrearchivo' => $archivoAdjunto,
                        'id_usu_cargarch' => $id_usuario,
                        'id_cliente' => $id_cliente,
                        'nitempresa' => $nitempresa
                    ]);

                }
                

                return $this->responseRequestSuccess($file);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
    }

    protected function responseRequestSuccess($ret)
    {
        return response()->json(['status' => 'success', 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['status' => 'error', 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
