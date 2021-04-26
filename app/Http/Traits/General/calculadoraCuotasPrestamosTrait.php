<?php


namespace App\Http\Traits\General;

use DB;
use App\Psformapago;
use App\PsEmpresa;


trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request)
    {

        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $id_forma_pago = $request->get('id_forma_pago');
        $formaPago = Psformapago::find( $id_forma_pago);
        $id_periodo_pago = $formaPago->id_periodo_pago;
        $sistemaPrestamo = $formaPago->codtipsistemap;

        if ($request->has('numcuotas') ){
            $numcuotas = $request->get('numcuotas');
        } else {
            $numcuotas = $formaPago->numcuotas;
        }

        if ($request->has('porcint') ){
            $porcint = $request->get('porcint'); 
        } else {
            $porcint = $formaPago->porcint;
        }
        
      

        if ($request->has('valorpres')) {
            $valorpres = $request->get('valorpres');
        } else {
            $valorpres = $formaPago->valorpres;
        }

        // Sistema frances
        if ($sistemaPrestamo == 1) {

            return $this->sistemaPrestMetodoFrances($request,$valorpres,$porcint,$id_periodo_pago,$numcuotas);

        // Sistema ingles
        } else if ($sistemaPrestamo == 2) {

            return $this->sistemaPrestMedodoIngles($request,$valorpres,$porcint,$id_periodo_pago,$numcuotas);

        } else {
            return [];
        }

       
    }

    function generarTablaAmortizacion ($request) {

        $nit_empresa = $request->get('nitempresa');

        $id_forma_pago = $request->get('id_forma_pago');
        $formaPago = Psformapago::find( $id_forma_pago);
        $sistemaPrestamo = $formaPago->codtipsistemap;

        $empresa = Psempresa::where('nitempresa',$nit_empresa )->first();

        $datos = $this->calcularCuota($request);

    
        if ( array_key_exists('tabla',$datos) ) {

            if ($sistemaPrestamo == 1) {

                foreach ($datos['tabla'] as $dato) {

                    $tabla[] = array(
                        'N° Cuota' => $dato['indice'],
                        'Fecha Cuota' =>  $dato['fecha_cuota_descrpcion'],
                        'Interes' => number_format($dato['interes'],2),
                        'Amortizacion' => number_format( $dato['amortizacion'],2),
                        'Saldo' => number_format($dato['saldo'],2),
                        'D. final' => number_format($dato['d_final'],2),
                        'Cuota Fija Mensual' => '$'.' '.number_format($dato['cfija_mensual'],2),
                        'Total a pagar por mes' =>   '$'.' '.number_format(round($dato['t_pagomes'],2),2)
        
                    );
        
                }
    
                return $tabla;

            } else if ($sistemaPrestamo == 2) {

                foreach ($datos['tabla'] as $dato) {

                    $tabla[] = array(
                        'N° Cuota' => $dato['indice'],
                        'Fecha Cuota' =>  $dato['fecha_cuota_descrpcion'],
                        'Interes' => number_format($dato['interes'],2),
                        'Capital' => '$'.' '.number_format($dato['cfija_mensual'],2),
                        'Total a pagar por mes' =>   '$'.' '.number_format(round($dato['t_pagomes'],2),2)
        
                    );
        
                }
    
                return $tabla; 

            }

            

        } else {
            return [];
        }
        

        



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

    function adicionarFechas ($date,$id_periodo_pago) {
        switch ($id_periodo_pago) {
            case 1:
                return $date->add(new \DateInterval('P1D'))->format('Y-m-d');
                break;
            case 2:
                return $date->add(new \DateInterval('P7D'))->format('Y-m-d');
                break;
            case 3:
                return $date->add(new \DateInterval('P15D'))->format('Y-m-d');
                break;
            case 4:
                return $date->add(new \DateInterval('P1M'))->format('Y-m-d');
                break;
            case 5:
                return $date->add(new \DateInterval('P1Y'))->format('Y-m-d');
                break;
            default:
                break;
        }
    }

    function sistemaPrestMetodoFrances($request,$valorpres,$porcint,$id_periodo_pago,$numcuotas) {

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
       // $tabla['tabla']['ind_renovar'] = 0;
        for ($x = 1; $x <= $numcuotas; $x++) {
            $fechas[$x] = $this->adicionarFechas($date,$id_periodo_pago);
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
                't_pagomes' => $valorCuota,
                'ind_renovar' => 0
            );
        }

        $tabla['datosprestamo'] = array(
        'tem' => $tem,
        'valor_cuota' => $valorCuota
        );

        return $tabla;
    }

    function sistemaPrestMedodoIngles ($request,$valorpres,$porcint,$id_periodo_pago,$numcuotas) {


        $fec_inicial = $request->get('fec_inicial');
        $fecha = $fec_inicial;
        $date = new \DateTime($fecha);
        $fechas[0] = $date->format('Y-m-d');
        $salestax = 0;
        $dpayment = 0;
        $capital[0] = $valorpres;
        $valorCuota = round($valorpres * ($porcint / 100));
        $tem = ($porcint / 100) / 12; // TASA EFECTIVA MENSUAL
        $tabla['tabla'][0]['cfija_mensual'] = 0;
        $tabla['tabla'][0]['indice'] =  '-';
        $tabla['tabla'][0]['fecha_cuota_descrpcion'] =   $this->SpanishDate( strtotime($fechas[0]));
        $tabla['tabla'][0]['fecha'] =    $fechas[0];
        $tabla['tabla'][0]['interes'] =     0;
        $tabla['tabla'][0]['amortizacion'] = 0;
        $tabla['tabla'][0]['saldo'] = 0;
        $tabla['tabla'][0]['d_final'] = 0;
        $tabla['tabla'][0]['cfija_mensual'] = 0;
        $tabla['tabla'][0]['ind_renovar'] = 0;

        for ($x = 1; $x < $numcuotas; $x++) {
            //$x++;
            $fechas[$x] = $this->adicionarFechas($date,$id_periodo_pago);
           
            $tabla['tabla'][] = array(
                'indice' => $x,
                'fecha_cuota_descrpcion' =>  $this->SpanishDate( strtotime($fechas[$x])),
                'fecha' => $fechas[$x],
                'interes' => $valorCuota,
                'amortizacion' => 0,
                'saldo' => 0,
                'd_final' => 0,
                'cfija_mensual' => 0,
                't_pagomes' => $valorCuota,
                'ind_renovar' => 0
            );
        }


        $fechas[$x] = $this->adicionarFechas($date,$id_periodo_pago);
        $tabla['tabla'][$x]['cfija_mensual'] = 0;
        $tabla['tabla'][$x]['indice'] =  $x;
        $tabla['tabla'][$x]['fecha'] =    $fechas[$x];
        $tabla['tabla'][$x]['fecha_cuota_descrpcion'] =   $this->SpanishDate( strtotime($fechas[$x]));
        
        $tabla['tabla'][$x]['interes'] =     $valorCuota;
        $tabla['tabla'][$x]['amortizacion'] = 0;
        $tabla['tabla'][$x]['saldo'] = 0;
        $tabla['tabla'][$x]['d_final'] = 0;
        $tabla['tabla'][$x]['cfija_mensual'] = $valorpres;
        $tabla['tabla'][$x]['t_pagomes'] = $valorpres + $valorCuota;
        $tabla['tabla'][$x]['ind_renovar'] = 1;
        // en esta ultima se agrega marca para hacer el nuevo ciclo completo




        $tabla['datosprestamo'] = array(
            'tem' => $tem,
            'valor_cuota' => $valorCuota
            );
    
            return $tabla;


    }


}
