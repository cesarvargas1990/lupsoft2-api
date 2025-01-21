<?php


namespace App\Http\Traits\General;

use DB;
use App\Psperiodopago;
use App\PsEmpresa;
use App\Pspstiposistemaprest;


trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $id_forma_pago = $request->get('id_forma_pago');
        $formaPago = Psperiodopago::find( $id_forma_pago);
        $id_periodo_pago = $formaPago->id;
        $sistemaPrestamo =$request->get('id_sistema_pago');
        $formula = Pspstiposistemaprest::where('codtipsistemap',$sistemaPrestamo)->first()->formula;
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
        $salida =  eval($formula);
        return $salida;
    }

    function generarTablaAmortizacion($request) {

        $nit_empresa = $request->get('nitempresa');
        $id_forma_pago = $request->get('id_forma_pago');
        $formaPago = Psperiodopago::find($id_forma_pago);
        $sistemaPrestamo = $request->get('id_sistema_pago');
        $empresa = Psempresa::where('nitempresa', $nit_empresa)->first();
        // Cálculo de los valores de la tabla de amortización
        return $this->calcularCuota($request)['tabla_formato'];
        
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

    function sistemaPrestMetodoFrances($request, $valorpres, $porcint, $id_periodo_pago, $numcuotas) {

        $fec_inicial = $request->get('fec_inicial');
        $fecha = $fec_inicial;
        $date = new \DateTime($fecha);
        // Configuraciones iniciales
        $fechas[1] = $date->format('Y-m-d');
        $capital[0] = $valorpres;
        $salestax = 0;
        $dpayment = 0;
        // Tasa efectiva mensual
        $tem = ($porcint / 100) / 12;
        // Cálculo de la cuota fija mensual
        $valorCuota = round(
            ($valorpres * ($salestax / 100 + 1) - $dpayment) 
            * ($tem) / (1 - pow((1 + $tem), (-$numcuotas))), 
            2
        );
        $tabla = [];
        for ($x = 1; $x <= $numcuotas; $x++) {
            // Calcular la fecha de la cuota
            $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
    
            // Calcular los valores de interés, amortización y saldo
            $int = round($capital[$x - 1] * $tem, 2); // Interés de la cuota
            $amort = round($valorCuota - $int, 2); // Amortización
    
            // Ajustar en la última cuota
            if ($x == $numcuotas) {
                // Forzar que la última cuota elimine cualquier saldo restante
                $amort = round($capital[$x - 1], 2); // Amortización igual al saldo restante
                $valorCuota = $amort + $int; // Ajustar la cuota para que coincida
                $capital[$x] = 0; // Saldo final en $0
            } else {
                // Reducir el saldo normalmente
                $capital[$x] = round($capital[$x - 1] - $amort, 2);
            }
    
            // Calcular D. Final (saldo después del pago de la cuota)
            $d_final = $capital[$x - 1] - $amort;
    
            // Agregar fila a la tabla
            $tabla['tabla'][] = array(
                'indice' => $x,
                'fecha_cuota_descrpcion' => $this->SpanishDate(strtotime($fechas[$x])),
                'fecha' => $fechas[$x],
                'interes' => $int,
                'amortizacion' => $amort,
                'saldo' => $capital[$x], // Saldo restante después de la cuota
                'cfija_mensual' => $valorCuota, // Cuota fija mensual
                't_pagomes' => $valorCuota      // Total a pagar (constante)
            );
        }
        // Agregar detalles del préstamo
        $tabla['datosprestamo'] = array(
            'tem' => $tem,
            'valor_cuota' => $valorCuota
        );
        return $tabla;
    }
    
    function sistemaPrestMedodoIngles($request, $valorpres, $porcint, $id_periodo_pago, $numcuotas) {
        $fec_inicial = $request->get('fec_inicial');
        $fecha = $fec_inicial;
        $date = new \DateTime($fecha);
        // Configuraciones iniciales
        $tem = ($porcint / 100) / 12; // Tasa efectiva mensual
        $interesFijo = round($valorpres * $tem, 2); // Intereses fijos en cada cuota
        $tabla = [];
        for ($x = 1; $x <= $numcuotas; $x++) {
            // Generar fechas de las cuotas
            $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
            if ($x < $numcuotas) {
                // Cuotas intermedias: solo intereses
                $tabla['tabla'][] = array(
                    'indice' => $x,
                    'fecha_cuota_descrpcion' => $this->SpanishDate(strtotime($fechas[$x])),
                    'fecha' => $fechas[$x],
                    'interes' => $interesFijo,
                    'amortizacion' => 0, // No se amortiza el capital
                    'saldo' => $valorpres, // El saldo permanece igual
                    'cfija_mensual' => 0, // Sin amortización en las cuotas intermedias
                    't_pagomes' => $interesFijo // Total a pagar por mes (solo intereses)
                );
            } else {
                // Última cuota: intereses + capital
                $tabla['tabla'][] = array(
                    'indice' => $x,
                    'fecha_cuota_descrpcion' => $this->SpanishDate(strtotime($fechas[$x])),
                    'fecha' => $fechas[$x],
                    'interes' => $interesFijo,
                    'amortizacion' => $valorpres, // Amortización completa del capital
                    'saldo' => 0, // El saldo se reduce a $0
                    'cfija_mensual' => $valorpres, // Se paga el capital completo
                    't_pagomes' => $interesFijo + $valorpres // Total: capital + intereses
                );
            }
        }

        return $tabla;
    }

}