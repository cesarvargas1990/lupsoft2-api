<?php


namespace App\Http\Traits\General;

use DB;


trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request)
    {

        //dd($request->all());
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");

        $valorpres = $request->get('valorpres');
        $numcuotas = $request->get('numcuotas');
        $porcint = $request->get('porcint'); // PORC INT ANUAL
        $id_forma_pago = $request->get('id_forma_pago');
        $valor_seguro = $request->get('valseguro');
        $fec_inicial = $request->get('fec_inicial');

        $fecha = $fec_inicial;
        $date = new \DateTime($fecha);
        $fechas[1] = $date->format('Y-m-d');

        $salestax = 0;
        $dpayment = 0;

        $capital[0] = $valorpres;

        $valorCuota =  round(  round(($valorpres * ($salestax / 100 + 1) - $dpayment) * (($porcint / 100) / 12) / (1 - pow((1 + ($porcint / 100) / 12), (-$numcuotas))), PHP_ROUND_HALF_DOWN) , 0);

        $tem = ($porcint / 100) / 12; // TASA EFECTIVA MENSUAL

        $tabla = [];

        for ($x = 2; $x <= $numcuotas; $x++) {
            switch ($id_forma_pago) {
                case 1:
                    $fechas[$x] = $date->add(new \DateInterval('P1D'))->format('Y-m-d');
                    break;
                case 2:
                    $fechas[$x] = $date->add(new \DateInterval('P7D'))->format('Y-m-d');
                    break;
                case 3:
                    $fechas[$x] = $date->add(new \DateInterval('P15D'))->format('Y-m-d');
                    break;
                case 4:
                    $fechas[$x] = $date->add(new \DateInterval('P1M'))->format('Y-m-d');
                    break;
                case 5:
                    $fechas[$x] = $date->add(new \DateInterval('P1Y'))->format('Y-m-d');
                    break;
                default:
                    break;
            }
        }




        for ($x = 1; $x <= $numcuotas; $x++) {

            $int = round($capital[$x - 1 ] * $tem);
            $amort = $valorCuota - $int;
            $capital[$x] = $capital[$x - 1 ] - $amort;
            $tabla['tabla'][] = array(
                'indice' => $x,
                'fecha_cuota_descrpcion' =>  $this->SpanishDate( strtotime($fechas[$x])),
                'fecha' => $fechas[$x],
                'interes' => $int,
                'amortizacion' => $amort,
                'saldo' => $capital[$x - 1 ],
                'd_final' => $capital[$x - 1 ] -  $valorCuota,
                'cfija_mensual' => $valorCuota,
                'c_seguro' => $valor_seguro,
                't_pagomes' => $valorCuota+ $valor_seguro

            );




        }

        $tabla['datosprestamo'] = array(
            'tem' => $tem,
            'valor_cuota' => $valorCuota
        );

        return $tabla;
    }

    function generarTablaAmortizacion ($request) {

        $datos = $this->calcularCuota($request);


        foreach ($datos['tabla'] as $dato) {

            $tabla[] = array(
                'N° Cuota' => $dato['indice'],
                'Fecha Cuota' =>  $dato['fecha_cuota_descrpcion'],
                'Interes' => number_format($dato['interes'],2),
                'Amortizacion' => number_format( $dato['amortizacion'],2),
                'Saldo' => number_format($dato['saldo'],2),
                'D. final' => number_format($dato['d_final'],2),
                'Cuota Fija Mensual' => '$'.' '.number_format($dato['cfija_mensual'],2),
                'Seguro Vida Mensual' => '$'.' '.number_format($dato['c_seguro'],2),
                'Total a pagar por mes' =>   '$'.' '.number_format(round($dato['t_pagomes'],2),2)

            );

        }

        return $tabla;



    }

    function SpanishDate($FechaStamp)
    {
        $ano = date('Y',$FechaStamp);
        $mes = date('n',$FechaStamp);
        $dia = date('d',$FechaStamp);

        $interes =
        $diasemana = date('w',$FechaStamp);
        $diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
            "Jueves","Viernes","Sábado");
        $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
            "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $diassemanaN[$diasemana].", $dia de ". $mesesN[$mes] ." de $ano";
    }


}
