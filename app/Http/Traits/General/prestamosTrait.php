<?php


namespace App\Http\Traits\General;

use DB;


trait prestamosTrait
{



    function guardarPrestamoFechas($request)
    {

        $datosCuota = $this->calcularCuota($request);



        $valor_cuota = $datosCuota['datosprestamo']['valor_cuota'];

        $now = new \DateTime();

        $now->getTimestamp();

        $id_prestamo = DB::table('psprestamos')->insertGetId(
            [
                'nitempresa' => $request->get('nitempresa'),
                'id_cliente' => $request->get('id_cliente'),
                'valorpres' => $request->get('valorpres'),
                'numcuotas' => $request->get('numcuotas'),
                'valcuota' => $valor_cuota,
                'porcint' => $request->get('porcint'),
                'id_forma_pago' => $request->get('id_forma_pago'),
                'valseguro' => $request->get('valseguro'),
                'fec_inicial' => date("Y-m-d", strtotime(str_replace('/', '-', $request->get('fec_inicial')))),
                'id_cobrador' => $request->get('id_cobrador'),
                'nitempresa' => $request->get('nitempresa'),
                'id_usureg' => $request->get('id_usureg'),
                'created_at' => $now
            ]
        );

        foreach ($datosCuota['tabla'] as $fechas) {

            DB::table('psfechaspago')->insert(
                [
                    'id_prestamo' => $id_prestamo,
                    'fecha_pago' => $fechas['fecha'],
                    'created_at' => $now
                ]
            );
        }



        return  $id_prestamo;
    }


    function obtenerQryListadoPrestamos($nit_empresa)
    {
        $qry = "
        SELECT 
        pre.*,
        cli.*,
        fp.*,
        em.*,
        ide.*,
        pp.*
        FROM 
        psprestamos pre ,
        psclientes cli, 
        psformapago fp, 
        psempresa em, 
        pstipodocidenti ide, 
        psperiodopago pp
        WHERE pre.nitempresa = :nit_empresa
        AND pre.id_forma_pago = fp.id
        AND pre.id_cliente = cli.id
        AND em.nitempresa = pre.nitempresa
        AND  cli.codtipdocid = ide.id
        AND fp.id_periodo_pago = pp.id";

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
        //dd($variables);
        $array = json_decode(json_encode($variables), true);
        $data = $this->getPlantillasDocumentos($request);
        $html_templates = [];
        if (count($data) > 0) {
            $renderTemplate = '';
  
            foreach ($data as $documento) {

                $renderTemplate = $this->replaceVariablesInTemplate($documento->plantilla_html, $array) . '<br>';

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
}
