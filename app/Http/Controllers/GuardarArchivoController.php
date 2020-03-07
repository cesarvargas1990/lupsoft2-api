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
        $extension = '';
        
        $path = $request->get('path');

        if ($request->has('image')) {

            try {
                if (!mime_content_type($request->get('image'))) {
                    $extension = 'jpeg';
                } else {
                    $extension = explode('/', mime_content_type($request->get('image')))[1];
                }
            } catch (\Exception $e) {
                    $extension = 'jpeg';
            }

            $destination_path = $path;

            if ($tdoc) {
                $archivoAdjunto = $tdoc . '-' . time() . '.' . $extension;
            } else {
                $archivoAdjunto = $customFilename;
            }


        
            if ($extension == "pdf") {
                
                $data = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '', $request->get('image')));

              
            } else {
                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->get('image')));
            }

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

    public function editarArchivoAdjunto(Request $request)
    {


        $id = $request->get('id');
        $tdoc = $request->get('id_tdocadjunto');
        $nitempresa = $request->get('nitempresa');
        $id_cliente = $request->get('id_cliente');
        $id_usuario = $request->get('id_usuario');
        $customFilename = $request->get('filename');
        $extension = '';
         
        $path = $request->get('path');

        foreach ($tdoc as $key => $i) {
            if ($request->has('image')) {


                $destination_path = $path;

                try {
                    if (!mime_content_type($request->get('image')[$i])) {
                        $extension = 'jpeg';
                    } else {
                        $extension = explode('/', mime_content_type($request->get('image')[$i]))[1];
                    }
                } catch (\Exception $e) {
                        $extension = 'jpeg';
                }
                
                
                if ($tdoc[$i]) {
                    $archivoAdjunto = $tdoc[$i] . '-' . time() . '.' . $extension;
                } else {
                    $archivoAdjunto = $customFilename;
                }

                if (array_key_exists($i, $request->get('image'))) {
                    

                

                    if ($extension == "pdf") { 
                        $data = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '', $request->get('image')[$i]));
                    } else {
                        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->get('image')[$i]));
                    }
                    
                   
                    if (!$this->validate_url($request->get('image')[$i])) { 
                        $file = $destination_path . $archivoAdjunto;
                    if (file_put_contents($file, $data)) {

                        $salida = DB::select('select count(1) cantidad from psdocadjuntos where id_cliente =:cliente and id_tdocadjunto=:tdocadjunto', [
                            'cliente' => $id_cliente,
                            'tdocadjunto' => $tdoc[$i]
                        ]);



                        if ($salida[0]->cantidad == 0) {




                            DB::table('psdocadjuntos')
                                ->insert([
                                    'id_cliente' => $id_cliente,
                                    'id_tdocadjunto' => $tdoc[$i],
                                    'rutaadjunto' => $file,
                                    'nombrearchivo' => $archivoAdjunto,
                                    'id_usu_cargarch' => $id_usuario,
                                    'nitempresa' => $nitempresa
                                ]);
                        } else {
                            if (!$tdoc[$i] == "") {


                                DB::table('psdocadjuntos')
                                    ->where('id_cliente', $id_cliente)
                                    ->where('id_tdocadjunto', $tdoc[$i])
                                    ->update([
                                        'rutaadjunto' => $file,
                                        'nombrearchivo' => $archivoAdjunto,
                                        'id_usu_cargarch' => $id_usuario,
                                        'nitempresa' => $nitempresa
                                    ]);
                            }
                        }



                        //$error[$i] = $this->responseRequestSuccess($file);
                    }

                    }
                    
                }
            }
        }
    }
    function validate_url($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);
    
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
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
