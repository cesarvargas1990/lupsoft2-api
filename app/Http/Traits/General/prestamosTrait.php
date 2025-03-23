<?php


namespace App\Http\Traits\General;

use DB;
use App\Psquerytabla;
use App\Psempresa;
use App\Pstdocplant;
use App\Psprestamos;
use App\Pspagos;
use App\Psperiodopago;
use App\Pspstiposistemaprest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
trait prestamosTrait
{

    function guardarPrestamoFechas($request,Psperiodopago $psperiodopago,Pspstiposistemaprest $pspstiposistemaprest)
    {

        $datosCuota = $this->calcularCuota($request,$psperiodopago,$pspstiposistemaprest);
        $valor_cuota = $datosCuota['datosprestamo']['valor_cuota']??0;
        $fechaHora = Carbon::parse($request->get('fecha'));
        $id_prestamo = DB::table('psprestamos')->insertGetId(
            [
                'id_empresa' => $request->get('id_empresa'),
                'id_cliente' => $request->get('id_cliente'),
                'valorpres' => $request->get('valorpres'),
                'numcuotas' => $request->get('numcuotas'),
                'valcuota' => $valor_cuota,
                'porcint' => $request->get('porcint'),
                'id_forma_pago' => $request->get('id_forma_pago'),
                'id_tipo_sistema_prest' => $request->get('id_sistema_pago'),
                'fec_inicial' => date("Y-m-d", strtotime(str_replace('/', '-', $request->get('fec_inicial')))),
                'id_cobrador' => $request->get('id_cobrador'),
                'id_empresa' => $request->get('id_empresa'),
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
                    'id_empresa' => $request->get('id_empresa'),
                ]
            );
        }
        return  $id_prestamo;
    }


    function obtenerQryListadoPrestamos($id_empresa)
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
        WHERE pre.id_empresa = :id_empresa
        AND pre.id_cliente = cli.id
        and pre.id_tipo_sistema_prest  = tsip.id
        and pp.id = pre.id_forma_pago
        AND em.id = pre.id_empresa
        AND  cli.id_tipo_docid = ide.id
        AND pre.ind_estado = 1";

        return $qry;
    }
    function consultaListadoPrestamos($id_empresa)
    {
        $qry = $this->obtenerQryListadoPrestamos($id_empresa);
        $binds = array(
            'id_empresa' => $id_empresa
        );
        $data = DB::select($qry, $binds);
        return $data;
    }

    function consultaVariablesPrestamo($id_empresa, $idprestamo)
    {

        $qry = $this->obtenerQryListadoPrestamos($id_empresa);
        $qry .= ' and pre.id = :id_prestamo';
        $binds = array(
            'id_empresa' => $id_empresa,
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

    function renderTemplate($request, Psquerytabla $psQueryTabla,Pstdocplant $pstdocplant)
    {
        $idprestamo = $request->get('id_prestamo');
        $id_empresa = $request->get('id_empresa');
        $variables = $this->consultaVariablesPrestamo($id_empresa, $idprestamo)[0];
        $array = json_decode(json_encode($variables), true);
        $data = $this->getPlantillasDocumentos($request,$pstdocplant);
        return $this->replaceVariables($data,$array,$id_empresa,$idprestamo,$psQueryTabla);
    }

    public function replaceVariables($data,$array,$id_empresa,$idprestamo,$psQueryTabla) {
        foreach ($data as $documento) {
            $renderTemplate = $this->replaceVariablesInTemplate($documento->plantilla_html, $array) . '<br>';
            $start_tag = '<!--QRT';
            $end_tag = 'QRT-->';
            if (preg_match_all('/' . preg_quote($start_tag) . '(.*?)' . preg_quote($end_tag) . '/s', $renderTemplate, $matches)) {
                $matches = ($matches[1]);
                $id_empresa = $id_empresa;
                $renderTemplate = $this->renderTemplate2($matches,$psQueryTabla,$id_empresa,$idprestamo,$renderTemplate);
            }
            $html_templates[] = array(
                'id' => $documento->id,
                'nombre' => $documento->nombre,
                'plantilla_html' => $renderTemplate,
                'id_empresa' => $id_empresa
            );
        }
        return $html_templates;
    }

    public function renderTemplate2($matches,$psQueryTabla,$id_empresa,$idprestamo,$renderTemplate){
        $str2 = '';
        foreach ($matches as $value) {
            $qt = $value[0];
            $str = ltrim($value, $qt);
            if ((preg_match_all('/' . preg_quote('[') . '(.*?)' . preg_quote(']') . '/s', $str, $matchesv))) {
                $qt = $psQueryTabla::where('codigo', $qt)->where('id_empresa', $id_empresa)->first()->sql;
                $vars = $this->consultaVariablesPrestamo($id_empresa, $idprestamo)[0];
                $array = json_decode(json_encode($vars), true);
                $qt = $this->replaceVariablesInTemplate($qt,$array);
                $query =  DB::select($qt);
                $variables = $matchesv[1];
                $str2 = $this->setVars($query,$variables,$str,$str2);
                
            }
            $renderTemplate = str_replace($matches[0], $str2, $renderTemplate);
            $renderTemplate = str_replace('<!--QRT', '',  $renderTemplate);
            $renderTemplate = str_replace('QRT-->', '',  $renderTemplate);
        }
        return $renderTemplate;
    }

    public function getPlantillasDocumentos($request, Pstdocplant $pstdocplant)
    {
        $id_empresa = $request->get('id_empresa');
        $data = $pstdocplant::where('id_empresa', $id_empresa)->get();
        return $data;
    }
  
    public function setVars($query,$variables,$str,$str2){
        foreach ($query as $val1) {
            $cadenaReemplazada = '';
            $cadenaSubstituir = $str;
            foreach ($variables as $key => $val2) {
                $valorAsubstituir = $val1->{$val2};
                $queSeVaASubstituir = '[' . $val2 . ']';
                $cadenaSubstituir = str_replace($queSeVaASubstituir, (string) $valorAsubstituir, $cadenaSubstituir);
            }
            $str2 .= $cadenaSubstituir;
        }
        return $str2;
    }
    public function getCapitalPrestado($id_empresa, Psprestamos $psPrestamos)
    {
        try {
            $valorpres = $psPrestamos->where('id_empresa', $id_empresa)
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


    public function getCapitalInicial($id_empresa, Psempresa $psempresa)
    {
        try {
            $capitalInicial = $psempresa::where('id', $id_empresa)
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

    public function getTotalCapital ($id_empresa, Psempresa $psempresa, Psprestamos $psprestamos) {
        $capitalinicial = $this->getCapitalInicial($id_empresa,$psempresa);
        $capitalPrestado = $this->getCapitalPrestado($id_empresa,$psprestamos);
        return $capitalinicial - $capitalPrestado;
    }  

    public function getTotalPrestadoHoy($request, Psprestamos $psprestamos)
    {
        try {
            $id_empresa = $request->get('id_empresa');
            $fecha = Carbon::createFromFormat('Y-m-d', $request->get('fecha'))->toDateString();
            $fecIni = Carbon::parse($fecha)->startOfDay();
            $fecFin = Carbon::parse($fecha)->endOfDay();
            $valorpres = $psprestamos::where('id_empresa', $id_empresa)
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


    public function getTotalintereseshoy($request,Pspagos $pspagos)
    {
        $id_empresa = $request->get('id_empresa');
        $fecha = Carbon::createFromFormat('Y-m-d', $request->get('fecha'))->toDateString();
        $fecIni = Carbon::parse($fecha)->startOfDay();
        $fecFin = Carbon::parse($fecha)->endOfDay();

    
        if ($this->getPerfilUser() == 1) {
            // Sumar todos los pagos de la empresa
            $data = $pspagos::where('id_empresa', $id_empresa)
                ->whereBetween('fecha_realpago', [$fecIni, $fecFin])
                ->where('ind_estado', 1)
                ->sum('valcuota');
        } else {
            // Sumar solo los pagos del usuario específico
            $data = $pspagos::where('id_empresa', $id_empresa)
                ->whereBetween('fecha_realpago', [$fecIni, $fecFin])
                ->where('ind_estado', 1)
                ->where('id_usureg', Auth::user()->id)
                ->sum('valcuota');
        }

        return $data ?? 0;
    }

    public function getPerfilUser()
    {
        return Auth::user()->perfiles->firstWhere('id', 1)->id ?? null;
    }

    public function getValorPrestamos( $request, Psprestamos $psprestamos)
    {
        try {
            $id_empresa = $request->get('id_empresa');

            $valorpres = $psprestamos::where('id_empresa', $id_empresa)
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

    public function getTotalintereses($request, Pspagos $pspagos)
    {
        try {
            $id_empresa = $request->get('id_empresa');
    
    
            if ($this->getPerfilUser() == 1) {
               
                $totalintereses = $pspagos::where('id_empresa', $id_empresa)
                    ->where('ind_estado', 1)
                    ->sum('valcuota');
            } else {
               
                $totalintereses = $pspagos::where('id_empresa', $id_empresa)
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

    public function totalPrestadoHoy ($id_empresa, Psempresa $psempresa, Psprestamos $psprestamos) {
        $capitalinicial = $this->getCapitalInicial($id_empresa,$psempresa);
        $capitalPrestado = $this->getCapitalPrestado($id_empresa,$psprestamos);
        return $capitalinicial + $capitalPrestado;
    }

    

    
}
