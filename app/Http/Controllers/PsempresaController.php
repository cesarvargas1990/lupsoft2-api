<?php

namespace App\Http\Controllers;

use App\PsEmpresa;

use Illuminate\Http\Request;

use DB;

class PsempresaController extends Controller
{
    private const TIPO_FIRMA = 3;

    public function __construct()
    {
        $this->middleware('auth');
    }



    public function showOnePsempresa(PsEmpresa $psempresa, $nid)
    {
        try {
            $data = $psempresa::where('id', $nid);
            $empresa = $data->first();
            $empresa = $this->normalizarFirmaEmpresa($empresa);
            return response()->json($empresa);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    protected function normalizarFirmaEmpresa($empresa)
    {
        if (!$empresa || !isset($empresa->firma) || empty($empresa->firma)) {
            return $empresa;
        }

        $firma = trim((string) $empresa->firma);
        if (preg_match('/^https?:\/\//i', $firma)) {
            return $empresa;
        }

        $baseUrl = '';
        $request = function_exists('app') ? app('request') : null;
        if ($request && method_exists($request, 'getSchemeAndHttpHost')) {
            $requestHost = rtrim((string) $request->getSchemeAndHttpHost(), '/');
            if ($requestHost !== '' && !preg_match('/\/\/(localhost|127\.0\.0\.1)(:\d+)?$/i', $requestHost)) {
                $baseUrl = $requestHost;
            }
        }

        if ($baseUrl === '') {
            $envBaseUrl = rtrim((string) env('APP_URL', ''), '/');
            if ($envBaseUrl !== '' && !preg_match('/\/\/(localhost|127\.0\.0\.1)(:\d+)?$/i', $envBaseUrl)) {
                $baseUrl = $envBaseUrl;
            }
        }

        if ($baseUrl !== '') {
            $empresa->firma = $baseUrl . '/' . ltrim($firma, '/');
        }

        return $empresa;
    }

    public function update($id, Request $request, PsEmpresa $psempresa)
    {
        try {
            $data = $psempresa::findOrFail($id);
            $request->request->add(['nitempresa' => $request->get('nit')]);

            $firma = $request->get('firma');
            if ($this->esBase64DataUri($firma)) {
                $request->request->add(['firma' => $this->guardarFirmaEmpresaBase64($id, $firma)]);
            } else {
                $request->request->add(['firma' => $this->normalizarFirmaEmpresaInput($firma)]);
            }

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

    protected function normalizarFirmaEmpresaInput($firma)
    {
        if (empty($firma)) {
            return $firma;
        }

        $firma = trim((string) $firma);
        $path = parse_url($firma, PHP_URL_PATH);

        if (is_string($path) && $path !== '') {
            $firma = $path;
        }

        $firma = ltrim($firma, '/');
        $pos = stripos($firma, 'upload/');
        if ($pos !== false) {
            $firma = substr($firma, $pos);
        }

        return $firma;
    }

    protected function esBase64DataUri($payload)
    {
        return is_string($payload) && preg_match('#^data:[^;]+;base64,#i', trim($payload));
    }

    protected function guardarFirmaEmpresaBase64($idEmpresa, $firmaBase64)
    {
        preg_match('#^data:([^;]+);base64,#i', $firmaBase64, $matches);
        $mimeType = isset($matches[1]) ? strtolower(trim($matches[1])) : 'image/png';
        $extension = $this->mapMimeToExtension($mimeType);

        $filename = self::TIPO_FIRMA . '-' . time() . '.' . $extension;
        $relativePath = 'upload/documentosAdjuntos/' . $filename;
        $basePath = rtrim(base_path('upload/documentosAdjuntos'), '/\\') . DIRECTORY_SEPARATOR;

        if (!is_dir($basePath) && !@mkdir($basePath, 0775, true) && !is_dir($basePath)) {
            throw new \RuntimeException('Cannot create upload directory');
        }

        $cleanData = preg_replace('#^data:[^;]+;base64,#i', '', $firmaBase64);
        $decodedData = base64_decode($cleanData, true);
        if ($decodedData === false) {
            throw new \RuntimeException('Invalid base64 payload');
        }

        $decodedData = $this->optimizarFirmaParaGuardar($decodedData, $mimeType, $extension);

        $result = @file_put_contents($basePath . $filename, $decodedData);
        if ($result === false) {
            throw new \RuntimeException('Cannot upload file');
        }

        return $relativePath;
    }

    protected function mapMimeToExtension($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return isset($map[$mimeType]) ? $map[$mimeType] : 'png';
    }

    protected function optimizarFirmaParaGuardar($binaryData, $mimeType, $extension)
    {
        if (!is_string($binaryData) || $binaryData === '' || strpos((string) $mimeType, 'image/') !== 0 || !extension_loaded('gd')) {
            return $binaryData;
        }

        $image = @imagecreatefromstring($binaryData);
        if ($image === false) {
            return $binaryData;
        }

        $wasCropped = false;
        [$image, $wasCropped] = $this->recortarAreaUtilSiAplica($image, $mimeType, $extension);

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

        if (!$wasCropped && strlen($optimized) >= strlen($binaryData)) {
            return $binaryData;
        }

        return $optimized;
    }

    protected function recortarAreaUtilSiAplica($image, $mimeType, $extension)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width <= 0 || $height <= 0) {
            return [$image, false];
        }

        $isTransparentFriendly = $mimeType === 'image/png'
            || $mimeType === 'image/webp'
            || $mimeType === 'image/gif'
            || $extension === 'png'
            || $extension === 'webp'
            || $extension === 'gif';

        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                $red = ($rgba >> 16) & 0xFF;
                $green = ($rgba >> 8) & 0xFF;
                $blue = $rgba & 0xFF;

                $isDarkEnough = ($red < 245 || $green < 245 || $blue < 245);
                $isInkPixel = $isTransparentFriendly
                    ? ($alpha < 120 && $isDarkEnough)
                    : $isDarkEnough;

                if (!$isInkPixel) {
                    continue;
                }

                if ($x < $minX) {
                    $minX = $x;
                }
                if ($y < $minY) {
                    $minY = $y;
                }
                if ($x > $maxX) {
                    $maxX = $x;
                }
                if ($y > $maxY) {
                    $maxY = $y;
                }
            }
        }

        if ($maxX < 0 || $maxY < 0) {
            return [$image, false];
        }

        $padding = 6;
        $cropX = max(0, $minX - $padding);
        $cropY = max(0, $minY - $padding);
        $cropW = min($width - $cropX, ($maxX - $minX + 1) + ($padding * 2));
        $cropH = min($height - $cropY, ($maxY - $minY + 1) + ($padding * 2));

        if ($cropW <= 0 || $cropH <= 0 || ($cropW === $width && $cropH === $height)) {
            return [$image, false];
        }

        $cropped = @imagecrop($image, [
            'x' => $cropX,
            'y' => $cropY,
            'width' => $cropW,
            'height' => $cropH,
        ]);

        if ($cropped === false) {
            return [$image, false];
        }

        imagedestroy($image);
        return [$cropped, true];
    }
}
