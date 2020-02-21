<?php


namespace App\Http\Traits\General;

use DB;


trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request)
    {


        $valorpres = $request->get('valorpres');
        $numcuotas = $request->get('numcuotas');
        $porcint = $request->get('porcint');
        $id_forma_pago = $request->get('id_forma_pago');
        $valor_seguro = $request->get('valseguro');
        $fec_inicial = $request->get('fec_inicial');

        $fecha = explode("/", $fec_inicial);
        $date = new \DateTime($fecha[2] . '-' . $fecha[1] . '-' . $fecha[0]);
        $fechas[1] = $date->format('Y-m-d');

        $salestax = 0;
        $dpayment = 0;

        $capital[0] = $valorpres;

        $valorCuota = round(($valorpres * ($salestax / 100 + 1) - $dpayment) * (($porcint / 100) / 12) / (1 - pow((1 + ($porcint / 100) / 12), (-$numcuotas))), 2);



        $tabla = [];

        for ($x = 2; $x <= $numcuotas; $x++) {
            switch ($id_forma_pago) {
                case 1:
                    $fechas[$x] = $date->add(new DateInterval('P1D'))->format('Y-m-d');
                    break;
                case 2:
                    $fechas[$x] = $date->add(new DateInterval('P7D'))->format('Y-m-d');
                    break;
                case 3:
                    $fechas[$x] = $date->add(new DateInterval('P15D'))->format('Y-m-d');
                    break;
                case 4:
                    $fechas[$x] = $date->add(new DateInterval('P1M'))->format('Y-m-d');
                    break;
                case 5:
                    $fechas[$x] = $date->add(new DateInterval('P1Y'))->format('Y-m-d');
                    break;
                default:
                    break;
            }
        }


        for ($x = 1; $x <= $numcuotas; $x++) {

            $tabla['fecha_pago'] = $fechas[$x]; // este valor es el que se va cambiando y se arma de acuerdo al periodo de pago
            $tabla['valor_cuota'] = $valorCuota;
            $tabla['fecha_registro'] = date('Y-m-d', time());
            $capital[$x] = $capital[$x - 1] - $valorCuota;
            $tabla['capital'] = $capital[$x];



        }

        return $tabla;
    }


}
