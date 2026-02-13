<?php

namespace App\Http\Controllers;

use App\PsEmpresa;

use Illuminate\Http\Request;

use DB;

class PsempresaController extends Controller
{
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
        $request = function_exists('request') ? request() : null;
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
            $request->request->add(['firma' => $this->normalizarFirmaEmpresaInput($request->get('firma'))]);
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
}
