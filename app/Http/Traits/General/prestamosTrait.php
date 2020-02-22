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
                [   'nitempresa' => $request->get('nitempresa'),
                    'id_cliente' => $request->get('id_cliente'),
                    'valorpres' => $request->get('valorpres'),
                    'numcuotas' => $request->get('numcuotas'),
                    'valcuota' => $valor_cuota,
                    'porcint' => $request->get('porcint'),
                    'codfpago' => $request->get('codfpago'),
                    'valseguro' => $request->get('valseguro'),
                    'fec_inicial' => date("Y-m-d", strtotime(str_replace('/', '-', $request->get('fec_inicial'))) ),
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


}
