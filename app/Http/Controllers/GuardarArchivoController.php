<?php

namespace App\Http\Controllers;

use App\Psclientes;

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
        $id_empresa = $request->get('id_empresa');
        $id_cliente = $request->get('id_cliente');
        $id_usuario = $request->get('id_usuario');
        $customFilename = $request->get('filename');
        $path = $request->get('path');

        if (!$request->has('image')) {
            return $this->responseRequestError('File not found');
        }

        $imageData = $request->get('image');

        // Determinar la extensión del archivo
        $extension = $this->obtenerExtensionArchivo($imageData);

        // Definir el nombre del archivo
        $archivoAdjunto = $tdoc ? "{$tdoc}-" . time() . ".{$extension}" : $customFilename;
        $basePath = $this->resolveUploadBasePath($path);
        if (!$this->ensureDirectoryExists($basePath)) {
            return $this->responseRequestError('Cannot create upload directory');
        }
        $rutaAdjunto = $this->buildRelativeAttachmentPath($archivoAdjunto);
        $filePath = $basePath . $archivoAdjunto;

        // Decodificar el archivo
        $data = $this->decodificarArchivoBase64($imageData, $extension);

        // Guardar el archivo
        if (!file_put_contents($filePath, $data)) {
            return $this->responseRequestError('Cannot upload file');
        }

        // Si hay un tipo de documento, guardar en la base de datos
        if (!empty($tdoc)) {
            DB::table('psdocadjuntos')->insertGetId([
                'rutaadjunto' => $rutaAdjunto,
                'id_tdocadjunto' => $tdoc,
                'nombrearchivo' => $archivoAdjunto,
                'id_usu_cargarch' => $id_usuario,
                'id_cliente' => $id_cliente,
                'id_empresa' => $id_empresa
            ]);
        }

        return $this->responseRequestSuccess($rutaAdjunto);
    }

    /**
     * Obtiene la extensión del archivo a partir del contenido en base64.
     */
    public function obtenerExtensionArchivo($imageData)
    {
        try {
            $mimeType = mime_content_type($imageData);
            return $mimeType ? explode('/', $mimeType)[1] : 'jpeg';
        } catch (\Exception $e) {
            return 'jpeg';
        }
    }

    /**
     * Decodifica el archivo base64 dependiendo de su tipo.
     */


    public function editarArchivoAdjunto(Request $request)
    {
        $id_cliente = $request->get('id_cliente');
        $id_usuario = $request->get('id_usuario');
        $id_empresa = $request->get('id_empresa');
        $path = $request->get('path');
        $customFilename = $request->get('filename');
        $tdocs = $request->get('id_tdocadjunto');
        $images = $request->get('image');

        foreach ($tdocs as $i => $tdoc) {
            if (!$request->has('image') || !isset($images[$i])) {
                continue;
            }

            // Obtener la extensión del archivo
            $extension = $this->obtenerExtensionArchivo($images[$i]);

            // Definir el nombre del archivo
            $archivoAdjunto = !empty($tdoc) ? "{$tdoc}-" . time() . ".{$extension}" : $customFilename;
            $basePath = $this->resolveUploadBasePath($path);
            if (!$this->ensureDirectoryExists($basePath)) {
                continue;
            }
            $rutaAdjunto = $this->buildRelativeAttachmentPath($archivoAdjunto);
            $filePath = $basePath . $archivoAdjunto;

            // Decodificar el archivo base64
            $data = $this->decodificarArchivoBase64($images[$i], $extension);

            // Validar si la imagen ya existe en la URL
            if ($this->validateUrl($images[$i])) {
                continue;
            }

            // Guardar el archivo
            if (!file_put_contents($filePath, $data)) {
                continue;
            }

            // Verificar si ya existe un archivo adjunto para el cliente y tipo de documento
            $existeAdjunto = DB::table('psdocadjuntos')
                ->where('id_cliente', $id_cliente)
                ->where('id_tdocadjunto', $tdoc)
                ->exists();

            if (!$existeAdjunto) {
                // Insertar nuevo archivo adjunto
                DB::table('psdocadjuntos')->insert([
                    'id_cliente' => $id_cliente,
                    'id_tdocadjunto' => $tdoc,
                    'rutaadjunto' => $rutaAdjunto,
                    'nombrearchivo' => $archivoAdjunto,
                    'id_usu_cargarch' => $id_usuario,
                    'id_empresa' => $id_empresa
                ]);
            } else {
                // Actualizar archivo adjunto existente
                DB::table('psdocadjuntos')
                    ->where('id_cliente', $id_cliente)
                    ->where('id_tdocadjunto', $tdoc)
                    ->update([
                        'rutaadjunto' => $rutaAdjunto,
                        'nombrearchivo' => $archivoAdjunto,
                        'id_usu_cargarch' => $id_usuario,
                        'id_empresa' => $id_empresa
                    ]);
            }
        }
    }



    /**
     * Decodifica el archivo base64 dependiendo de su tipo.
     */
    public function decodificarArchivoBase64($imageData, $extension)
    {
        $pattern = ($extension === "pdf") ? '#^data:application/\w+;base64,#i' : '#^data:image/\w+;base64,#i';
        return base64_decode(preg_replace($pattern, '', $imageData));
    }
    public function validateUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    private function resolveUploadBasePath($path)
    {
        // Se mantiene el parametro por compatibilidad, pero la ruta de adjuntos es fija.
        return rtrim(storage_path('app/upload/documentosAdjuntos'), '/\\') . DIRECTORY_SEPARATOR;
    }

    private function ensureDirectoryExists($dir)
    {
        if (is_dir($dir)) {
            return true;
        }

        return @mkdir($dir, 0775, true) || is_dir($dir);
    }

    private function buildRelativeAttachmentPath($filename)
    {
        return 'upload/documentosAdjuntos/' . ltrim($filename, '/\\');
    }



    public function responseRequestSuccess($ret)
    {
        return response()->json(['status' => 'success', 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    public function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['status' => 'error', 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
