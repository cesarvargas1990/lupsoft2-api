<?php


namespace App\Http\Traits\General;

use DB;
use App\Psquerytabla;
use App\Psperiodopago;
use App\Psempresa;

trait prestamosTrait
{



    function guardarPrestamoFechas($request)
    {

        $datosCuota = $this->calcularCuota($request);



        $valor_cuota = $datosCuota['datosprestamo']['valor_cuota']??0;

        $now = new \DateTime();

        $now->getTimestamp();


        $formaPago = Psperiodopago::find( $request->get('id_forma_pago'));
      

        

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
                'created_at' => $now,
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
                    'created_at' => $now,
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
        pre.*,
        cli.*,
        em.*,
        ide.*,
        pp.*,
        pp.nomperiodopago nomfpago
        FROM 
        psprestamos pre ,
        psclientes cli, 
        psempresa em, 
        pstipodocidenti ide, 
        psperiodopago pp
        WHERE pre.nitempresa = :nit_empresa
        AND pre.id_cliente = cli.id
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

    function consultaVariablesPrestamo($nit_empresa, $id_prestamo)
    {

        $qry = $this->obtenerQryListadoPrestamos($nit_empresa);

        $qry .= ' and pre.id = :id_prestamo';

        $binds = array(
            'nit_empresa' => $nit_empresa,
            'id_prestamo' => $id_prestamo
        );


        $data = DB::select($qry, $binds);

        return $data;
    }

    public function replaceVariablesInTemplate($template, array $variables)
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

    public function renderTemplate($request)
    {
        $id_prestamo = $request->get('id_prestamo');
        $nit_empresa = $request->get('nitempresa');
        $variables = $this->consultaVariablesPrestamo($nit_empresa, $id_prestamo)[0];

        $array = json_decode(json_encode($variables), true);
        $data = $this->getPlantillasDocumentos($request);
        $html_templates = [];
        if (count($data) > 0) {
            $renderTemplate = '';

            foreach ($data as $documento) {

                $renderTemplate = $this->replaceVariablesInTemplate($documento->plantilla_html, $array) . '<br>';


                $start_tag = '<!--QRT';
                $end_tag = 'QRT-->';


                if (preg_match_all('/' . preg_quote($start_tag) . '(.*?)' . preg_quote($end_tag) . '/s', $renderTemplate, $matches)) {

                    $matches = ($matches[1]);

                    $nitempresa = $nit_empresa;
                    $str2 = '';
                    foreach ($matches as $value) {

                        $qt = $value[0];
                        $str = ltrim($value, $qt);

                        if ((preg_match_all('/' . preg_quote('[') . '(.*?)' . preg_quote(']') . '/s', $str, $matchesv))) {

                            $qt = Psquerytabla::where('codigo', $qt)->where('nitempresa', $nitempresa)->first()->sql;


                            $vars = $this->consultaVariablesPrestamo($nit_empresa, $id_prestamo)[0];

                            $array = json_decode(json_encode($vars), true);

                            $qt = $this->replaceVariablesInTemplate($qt,$array);
                            
                            $query =  DB::select($qt);
                            $variables = $matchesv[1];



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
                        }


                        $renderTemplate = str_replace($matches[0], $str2, $renderTemplate);
                        $renderTemplate = str_replace('<!--QRT', '',  $renderTemplate);
                        $renderTemplate = str_replace('QRT-->', '',  $renderTemplate);
                    }
                }



                $html_templates[] = array(
                    'id' => $documento->id,
                    'nombre' => $documento->nombre,
                    'plantilla_html' => $renderTemplate,
                    'nit_empresa' => $nit_empresa
                );
            }
        }

        return $html_templates;
    }



    public function getPlantillasDocumentos($request)
    {
        $nit_empresa = $request->get('nitempresa');
        $qry = "select * from pstdocplant where nitempresa = :nitempresa";
        $binds = array(

            'nitempresa' => $nit_empresa

        );
        $data = DB::select($qry, $binds);

        return $data;
    }
  

    public function getCapitalPrestado ($nitempresa) {  

        $qry= "select  sum(p2.valorpres) valorpres

			from psprestamos p2 where p2.nitempresa  = :nit_empresa 
				and p2.ind_estado = 1 "; 
       $binds = array(  
           'nit_empresa'=>  $nitempresa
       );
        $data = DB::select($qry,$binds);
        $valorpres = $data[0]->valorpres;
      	return (float) $valorpres;
    }

    public function getCapitalInicial ($nitempresa) {
        $qry =  "SELECT vlr_capinicial FROM psempresa WHERE nitempresa = :nit_empresa";
       $binds = array(
           'nit_empresa'=>  $nitempresa
       );
        $data = DB::select($qry,$binds);
        
        return $data[0]->vlr_capinicial; 
    }

    public function getTotalCapital ($nit_empresa) {
        
        $capitalinicial = $this->getCapitalInicial($nit_empresa);
        $capitalPrestado = $this->getCapitalPrestado($nit_empresa);
        return $capitalinicial - $capitalPrestado;
        
    }  

    public function getTotalPrestadoHoy ($request) {


        $nitempresa = $request->get('nitempresa');
        $fecha = $request->get('fecha');
        $fecIni = strtotime($fecha.' 00:00:00'); 
        $fecFin = strtotime($fecha.' 23:59:59'); 
        

         $qry =  "select sum(valorpres) valorpres from psprestamos 
         WHERE nitempresa = :nit_empresa
         and UNIX_TIMESTAMP(created_at) >= :fec_ini
         and UNIX_TIMESTAMP(created_at) <= :fec_fin
         and ind_estado = 1
         "; 
        $binds = array(
            'nit_empresa'=>  $nitempresa,
            'fec_ini' => $fecIni,
            'fec_fin' => $fecFin
        );
         $data = DB::select($qry,$binds);
         
         return $data[0]->valorpres; 

    }




    public function getTotalintereseshoy ($request) {

        $nitempresa = $request->get('nitempresa');
        $fecha = $request->get('fecha');
        $fecIni = strtotime($fecha.' 00:00:00'); 
        $fecFin = strtotime($fecha.' 23:59:59'); 
        
         $qry =  "select sum(valcuota) totalintereseshoy from pspagos 
         WHERE nitempresa = :nit_empresa
         and UNIX_TIMESTAMP(fecha_realpago) >= :fec_ini
         and UNIX_TIMESTAMP(fecha_realpago) <= :fec_fin
         and ind_estado = 1
         "; 
        $binds = array(
            'nit_empresa'=>  $nitempresa,
            'fec_ini' => $fecIni,
            'fec_fin' => $fecFin 
        );
        
         $data = DB::select($qry,$binds);
         
         return $data[0]->totalintereseshoy; 

    }


    public function getValorPrestamos ($request) {
        $nitempresa = $request->get('nitempresa');
        $qry = "select sum(valorpres) valorpres from psprestamos p where nitempresa = :nit_empresa and ind_estado =1";
        $binds = array(
            'nit_empresa'=>  $nitempresa
        
        );
        $data = DB::select($qry,$binds);
        return $data[0]->valorpres; 
    }

    public function getTotalintereses ($request) {

        $nitempresa = $request->get('nitempresa');
        
         
         $qry =  "select sum(valcuota) totalintereses from pspagos 
         WHERE nitempresa = :nit_empresa and ind_estado = 1"; 
        $binds = array(
            'nit_empresa'=>  $nitempresa
        
        );
        
         $data = DB::select($qry,$binds);
         
         return $data[0]->totalintereses; 

    }


    public function totalPrestadoHoy ($nit_empresa) {
        
        $capitalinicial = $this->getCapitalInicial($nit_empresa);
        $capitalPrestado = $this->getCapitalPrestado($nit_empresa);
        return $capitalinicial + $capitalPrestado;
        
    }
}
