<?php

namespace App\Http\Controllers;

use App\Psclientes;
use App\PsEmpresa;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Pstdocadjuntos;

class GuardarArchivoController extends Controller
{
    private const TIPO_FIRMA = 3;

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

        if (!$request->has('image')) {
            return $this->responseRequestError('File not found');
        }

        $imageData = $request->get('image');

        // Determinar tipo y extensión del archivo
        [$extension, $mimeType] = $this->detectarTipoArchivo($imageData, $customFilename);

        // Definir el nombre del archivo
        $archivoAdjunto = $tdoc
            ? "{$tdoc}-" . time() . ".{$extension}"
            : self::TIPO_FIRMA . '-' . time() . ".{$extension}";
        $basePath = $this->resolveUploadBasePath();
        if (!$this->ensureDirectoryExists($basePath)) {
            return $this->responseRequestError('Cannot create upload directory');
        }
        $rutaAdjunto = $this->buildRelativeAttachmentPath($archivoAdjunto);
        $filePath = $basePath . $archivoAdjunto;

        // Decodificar el archivo
        $decodedData = $this->decodificarArchivoBase64($imageData, $extension);
        if ($decodedData === false) {
            return $this->responseRequestError('Invalid base64 payload');
        }

        $data = $this->optimizarArchivoParaGuardar($decodedData, $mimeType, $extension);

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
        } elseif (!empty($id_empresa)) {
            PsEmpresa::where('id', $id_empresa)->update(['firma' => $rutaAdjunto]);
        }

        return $this->responseRequestSuccess($rutaAdjunto);
    }

    /**
     * Obtiene la extensión del archivo a partir del contenido en base64.
     */
    public function obtenerExtensionArchivo($imageData)
    {
        [$extension] = $this->detectarTipoArchivo($imageData);
        return $extension;
    }

    /**
     * Decodifica el archivo base64 dependiendo de su tipo.
     */


    public function editarArchivoAdjunto(Request $request)
    {
        $id_cliente = $request->get('id_cliente');
        $id_usuario = $request->get('id_usuario');
        $id_empresa = $request->get('id_empresa');
        $customFilename = $request->get('filename');
        $tdocs = $request->get('id_tdocadjunto');
        $images = $request->get('image');

        if (!$request->has('image') || !is_array($images) || !is_array($tdocs)) {
            return;
        }

        $basePath = $this->resolveUploadBasePath();
        if (!$this->ensureDirectoryExists($basePath)) {
            return;
        }

        foreach ($tdocs as $i => $tdoc) {
            if (!isset($images[$i])) {
                continue;
            }

            if ($this->validateUrl($images[$i])) {
                continue;
            }

            [$extension, $mimeType] = $this->detectarTipoArchivo($images[$i], $customFilename);
            $archivoAdjunto = !empty($tdoc) ? "{$tdoc}-" . time() . ".{$extension}" : $customFilename;
            $rutaAdjunto = $this->buildRelativeAttachmentPath($archivoAdjunto);
            $filePath = $basePath . $archivoAdjunto;

            $decodedData = $this->decodificarArchivoBase64($images[$i], $extension);
            if ($decodedData === false) {
                continue;
            }

            $data = $this->optimizarArchivoParaGuardar($decodedData, $mimeType, $extension);
            if (!file_put_contents($filePath, $data)) {
                continue;
            }

            DB::table('psdocadjuntos')->updateOrInsert(
                [
                    'id_cliente' => $id_cliente,
                    'id_tdocadjunto' => $tdoc,
                ],
                [
                    'rutaadjunto' => $rutaAdjunto,
                    'nombrearchivo' => $archivoAdjunto,
                    'id_usu_cargarch' => $id_usuario,
                    'id_empresa' => $id_empresa,
                ]
            );
        }
    }

    /**
     * Decodifica el archivo base64 dependiendo de su tipo.
     */
    public function decodificarArchivoBase64($imageData, $extension)
    {
        $cleanData = preg_replace('#^data:[^;]+;base64,#i', '', $imageData);
        return base64_decode($cleanData, true);
    }
    public function validateUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    private function detectarTipoArchivo($imageData, $customFilename = null)
    {
        $mimeType = null;

        if (is_string($imageData) && preg_match('#^data:([^;]+);base64,#i', $imageData, $matches)) {
            $mimeType = strtolower(trim($matches[1]));
        }

        if (!$mimeType && !empty($customFilename)) {
            $extension = strtolower(pathinfo($customFilename, PATHINFO_EXTENSION));
            if (!empty($extension)) {
                return [$extension, $this->mapExtensionToMime($extension)];
            }
        }

        if (!$mimeType) {
            $mimeType = 'image/jpeg';
        }

        return [$this->mapMimeToExtension($mimeType), $mimeType];
    }

    private function mapMimeToExtension($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];

        return isset($map[$mimeType]) ? $map[$mimeType] : 'jpg';
    }

    private function mapExtensionToMime($extension)
    {
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
        ];

        return isset($map[$extension]) ? $map[$extension] : 'application/octet-stream';
    }

    private function optimizarArchivoParaGuardar($binaryData, $mimeType, $extension)
    {
        if (!is_string($binaryData) || $binaryData === '') {
            return $binaryData;
        }

        if (strpos($mimeType, 'image/') !== 0 || !extension_loaded('gd')) {
            return $binaryData;
        }

        $image = @imagecreatefromstring($binaryData);
        if ($image === false) {
            return $binaryData;
        }

        $optimized = null;
        if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg' || $extension === 'jpg' || $extension === 'jpeg') {
            ob_start();
            imagejpeg($image, null, 75);
            $optimized = ob_get_clean();
        } elseif ($mimeType === 'image/png' || $extension === 'png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            ob_start();
            imagepng($image, null, 6);
            $optimized = ob_get_clean();
        } elseif (($mimeType === 'image/webp' || $extension === 'webp') && function_exists('imagewebp')) {
            ob_start();
            imagewebp($image, null, 75);
            $optimized = ob_get_clean();
        }

        imagedestroy($image);

        if (!is_string($optimized) || $optimized === '') {
            return $binaryData;
        }

        if (strlen($optimized) >= strlen($binaryData)) {
            return $binaryData;
        }

        return $optimized;
    }

    private function resolveUploadBasePath()
    {
        // La ruta de adjuntos es fija.
        return rtrim(base_path('upload/documentosAdjuntos'), '/\\') . DIRECTORY_SEPARATOR;
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
