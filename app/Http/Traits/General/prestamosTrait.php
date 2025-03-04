<?php


namespace App\Http\Traits\General;

use DB;
use App\Psquerytabla;
use App\Psempresa;
use App\Pstdocplant;
use App\Psprestamos;
use App\Pspagos;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
trait prestamosTrait
{

    function guardarPrestamoFechas($request)
    {

        $datosCuota = $this->calcularCuota($request);
        $valor_cuota = $datosCuota['datosprestamo']['valor_cuota']??0;
        $fechaHora = Carbon::parse($request->get('fecha'));
        $id_prestamo = DB::table('psprestamos')->insertGetId(
            [
                'nitempresa' => $request->get('nitempresa'),
                'id_cliente' => $request->get('id_cliente'),
                'valorpres' => $request->get('valorpres'),
                'numcuotas' => $request->get('numcuotas'),
                'valcuota' => $valor_cuota,
                'porcint' => $request->get('porcint'),
                'id_forma_pago' => $request->get('id_forma_pago'),
                'codtipsistemap' => $request->get('id_sistema_pago'),
                'fec_inicial' => date("Y-m-d", strtotime(str_replace('/', '-', $request->get('fec_inicial')))),
                'id_cobrador' => $request->get('id_cobrador'),
                'nitempresa' => $request->get('nitempresa'),
                'id_usureg' => $request->get('id_usureg'),
                'created_at' => $fechaHora,
                'ind_estado' => 1
            ]
        );

        foreach ($datosCuota['tabla'] as $fechas) {

            DB::table('psfechaspago')->insert(
                [
                    'id_prestamo' => $id_prestamo,
                    'fecha_pago' => $fechas['fecha'],
                    'valor_cuota' => $fechas['interes'],
                    'valor_pagar' => $fechas['t_pagomes'],
                    'ind_renovar' => $fechas['ind_renovar']??0,
                    'created_at' => $fechaHora,
                    'ind_estado' => 1,
                    'id_cliente' => $request->get('id_cliente'),
                    'nitempresa' => $request->get('nitempresa'),
                ]
            );
        }
        return  $id_prestamo;
    }


    function obtenerQryListadoPrestamos($nit_empresa)
    {
        $qry = "
        SELECT 
        date_format(CURDATE(),'%d/%m/%Y') fecha_actual,
        date_format(CURRENT_TIME(), '%H:%i:%s %p') hora_actua,
        pre.id id_prestamo,
        format(pre.valorpres,2) valorpresf,
        pre.*,
        cli.*,
        em.*,
        ide.*,
        tsip.*,
        pp.*,
        pp.nomperiodopago nomfpago
        FROM 
        psprestamos pre ,
        psclientes cli, 
        psempresa em, 
        pstipodocidenti ide, 
        pstiposistemaprest tsip,
        psperiodopago pp
        WHERE pre.nitempresa = :nit_empresa
        AND pre.id_cliente = cli.id
        and pre.codtipsistemap  = tsip.codtipsistemap 
        and pp.id = pre.id_forma_pago
        AND em.nitempresa = pre.nitempresa
        AND  cli.codtipdocid = ide.id
        AND pre.ind_estado = 1";

        return $qry;
    }
    function consultaListadoPrestamos($nit_empresa)
    {
        $qry = $this->obtenerQryListadoPrestamos($nit_empresa);
        $binds = array(
            'nit_empresa' => $nit_empresa
        );
        $data = DB::select($qry, $binds);
        return $data;
    }

    function consultaVariablesPrestamo($nitempresa, $idprestamo)
    {

        $qry = $this->obtenerQryListadoPrestamos($nitempresa);
        $qry .= ' and pre.id = :id_prestamo';
        $binds = array(
            'nit_empresa' => $nitempresa,
            'id_prestamo' => $idprestamo
        );
        return DB::select($qry, $binds);
    }

    function replaceVariablesInTemplate($template, array $variables)
    {

        return preg_replace_callback(
            '#{(.*?)}#',
            function ($match) use ($variables) {
                $match[1] = trim($match[1], '$');
                return $variables[$match[1]];
            },
            $template
        );
    }

    public function renderTemplate($id_prestamo, $nit_empresa, $documentos, $psQueryTabla, $psPrestamos)
    {
        // Consultar variables del préstamo
        $variables = $psPrestamos->consultaVariablesPrestamo($nit_empresa, $id_prestamo)[0];
        $variablesArray = json_decode(json_encode($variables), true);

        $html_templates = [];

        foreach ($documentos as $documento) {
            $renderTemplate = $this->replaceVariablesInTemplate($documento->plantilla_html, $variablesArray) . '<br>';
            $renderTemplate = $this->procesarQRT($renderTemplate, $nit_empresa, $id_prestamo, $psQueryTabla, $psPrestamos);

            $html_templates[] = [
                'id' => $documento->id,
                'nombre' => $documento->nombre,
                'plantilla_html' => $renderTemplate,
                'nit_empresa' => $nit_empresa
            ];
        }

        return $html_templates;
    }

/**
 * Procesa las etiquetas QRT en la plantilla HTML.
 */
    private function procesarQRT($template, $nit_empresa, $id_prestamo, $psQueryTabla, $psPrestamos)
    {
        $start_tag = '<!--QRT';
        $end_tag = 'QRT-->';
        
        if (preg_match_all('/' . preg_quote($start_tag) . '(.*?)' . preg_quote($end_tag) . '/s', $template, $matches)) {
            $matches = $matches[1];
            $processedTemplate = $template;

            foreach ($matches as $value) {
                $qt = $value[0];
                $str = ltrim($value, $qt);

                if (preg_match_all('/' . preg_quote('[') . '(.*?)' . preg_quote(']') . '/s', $str, $matchesv)) {
                    // Obtener SQL desde Psquerytabla
                    $queryData = $psQueryTabla->where('codigo', $qt)->where('nitempresa', $nit_empresa)->first();
                    if (!$queryData) {
                        continue;
                    }

                    $qt = $queryData->sql;

                    // Consultar variables del préstamo
                    $vars = $psPrestamos->consultaVariablesPrestamo($nit_empresa, $id_prestamo)[0];
                    $varsArray = json_decode(json_encode($vars), true);

                    // Reemplazar variables dentro de la consulta
                    foreach ($matchesv[1] as $var) {
                        if (isset($varsArray[$var])) {
                            $qt = str_replace("[$var]", $varsArray[$var], $qt);
                        }
                    }

                    // Reemplazar en la plantilla
                    $processedTemplate = str_replace($start_tag . $value . $end_tag, $qt, $processedTemplate);
                }
            }
            return $processedTemplate;
        }
        return $template;
    }

    public function getPlantillasDocumentos($request)
    {
        $nit_empresa = $request->get('nitempresa');
        $data = Pstdocplant::where('nitempresa', $nit_empresa)->get();
        return $data;
    }
  

    public function getCapitalPrestado($nitempresa, Psprestamos $psPrestamos)
    {
        try {
            $valorpres = $psPrestamos->where('nitempresa', $nitempresa)
                                    ->where('ind_estado', 1)
                                    ->sum('valorpres');

            return (float) $valorpres; // Retorna un número en caso de éxito

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500); // Devuelve una respuesta JSON en caso de error
        }
    }


    public function getCapitalInicial($nitempresa)
    {
        try {
            $capitalInicial = Psempresa::where('nitempresa', $nitempresa)
                                    ->value('vlr_capinicial');
            return $capitalInicial;
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function getTotalCapital ($nit_empresa) {
        $capitalinicial = $this->getCapitalInicial($nit_empresa);
        $capitalPrestado = $this->getCapitalPrestado($nit_empresa);
        return $capitalinicial - $capitalPrestado;
    }  

    public function getTotalPrestadoHoy($request)
    {
        try {
            $nitempresa = $request->get('nitempresa');
            $fecha = Carbon::createFromFormat('Y-m-d', $request->get('fecha'))->toDateString();
            $fecIni = Carbon::parse($fecha)->startOfDay();
            $fecFin = Carbon::parse($fecha)->endOfDay();
            $valorpres = Psprestamos::where('nitempresa', $nitempresa)
                                    ->whereBetween('created_at', [$fecIni, $fecFin])
                                    ->where('ind_estado', 1)
                                    ->sum('valorpres');

            return $valorpres;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    public function getTotalintereseshoy($request)
    {
        $nitempresa = $request->get('nitempresa');
        $fecha = Carbon::createFromFormat('Y-m-d', $request->get('fecha'))->toDateString();
        $fecIni = Carbon::parse($fecha)->startOfDay();
        $fecFin = Carbon::parse($fecha)->endOfDay();

        $perfil = Auth::user()->perfiles->firstWhere('id', 1)->id ?? null;
        
        if ($perfil == 1) {
            // Sumar todos los pagos de la empresa
            $data = Pspagos::where('nitempresa', $nitempresa)
                ->whereBetween('fecha_realpago', [$fecIni, $fecFin])
                ->where('ind_estado', 1)
                ->sum('valcuota');
        } else {
            // Sumar solo los pagos del usuario específico
            $data = Pspagos::where('nitempresa', $nitempresa)
                ->whereBetween('fecha_realpago', [$fecIni, $fecFin])
                ->where('ind_estado', 1)
                ->where('id_usureg', Auth::user()->id)
                ->sum('valcuota');
        }

        return $data ?? 0;
    }

    public function getValorPrestamos( $request)
    {
        try {
            $nitempresa = $request->get('nitempresa');

            $valorpres = Psprestamos::where('nitempresa', $nitempresa)
                                    ->where('ind_estado', 1)
                                    ->sum('valorpres');

            return $valorpres;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function getTotalintereses($request)
    {
        try {
            $nitempresa = $request->get('nitempresa');
    
            // Verificar si el usuario tiene asignado el perfil con ID 1
            $hasPerfil = Auth::user()->perfiles->contains('id', 1);
    
            if ($hasPerfil) {
               
                $totalintereses = Pspagos::where('nitempresa', $nitempresa)
                    ->where('ind_estado', 1)
                    ->sum('valcuota');
            } else {
               
                $totalintereses = Pspagos::where('nitempresa', $nitempresa)
                    ->where('ind_estado', 1)
                    ->where('id_usureg', Auth::user()->id)
                    ->sum('valcuota');
            }
    
            return $totalintereses;
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file'      => $e->getFile()
            ], 500);
        }
    }

    public function totalPrestadoHoy ($nit_empresa) {
        $capitalinicial = $this->getCapitalInicial($nit_empresa);
        $capitalPrestado = $this->getCapitalPrestado($nit_empresa);
        return $capitalinicial + $capitalPrestado;
    }

    

    
}
