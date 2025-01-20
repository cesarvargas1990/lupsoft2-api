<?php


namespace App\Http\Traits\General;

use DB;
use App\Psformapago;
use App\PsEmpresa;
use App\Pspstiposistemaprest;

trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request){

        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $id_forma_pago = $request->get('id_forma_pago');
        $formaPago = Psformapago::find( $id_forma_pago);
        $id_periodo_pago = $formaPago->id_periodo_pago;
        $sistemaPrestamo =$request->get('id_sistema_pago');

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
        // todo: Implementar logica sistema aleman u otro
            return [];
        }
    }

    function generarTablaAmortizacion($request) {
        $nit_empresa = $request->get('nitempresa');
        $id_forma_pago = $request->get('id_forma_pago');
        $id_sistema_pago = $request->get('id_sistema_pago');
    
        // Obtener datos de la empresa y forma de pago
        $formaPago = Psformapago::find($id_forma_pago);
        $empresa = Psempresa::where('nitempresa', $nit_empresa)->first();
    
        // Calcular la tabla de amortización con el sistema seleccionado
        $datos = $this->obtengoTablaresumenPrestamos($id_sistema_pago, $request, 
            $request->get('valorpres'), 
            $request->get('porcint'), 
            $request->get('id_forma_pago'), 
            $request->get('numcuotas')
        );
    
        // Validar si hay datos de amortización generados
        if (array_key_exists('tabla', $datos)) {
            $tabla = [];
    
            // Construir tabla de amortización formateada
            foreach ($datos['tabla'] as $dato) {
                $is_last_cuota = ($dato['indice'] == $request->get('numcuotas')); // Verificar si es la última cuota
            
                $tabla[] = array(
                    'N° Cuota' => $dato['indice'],
                    'Fecha Cuota' => $dato['fecha_cuota_descrpcion'],
                    'Interes' => '$' . ' ' . number_format($dato['interes'], 2),
                    'Amortizacion' => '$' . ' ' . number_format($dato['amortizacion'], 2),
                    'Saldo' => '$' . ' ' . number_format($dato['saldo']??0, 2),
                    'Cuota Fija Mensual' => '$' . ' ' . number_format($dato['cfija_mensual'] ?? 0, 2),
                    'Total a pagar por mes' => '$' . ' ' . number_format($dato['t_pagomes'], 2),
                );
            }
    
            return $tabla;
        } else {
            return [];
        }
    }
    


    function obtengoTablaresumenPrestamos($idSistemaPrest, $request, $valorpres, $porcint, $id_periodo_pago, $numcuotas) {
        // Obtener la fórmula desde la base de datos usando Eloquent
        $sistema = Pspstiposistemaprest::where('codtipsistemap',$idSistemaPrest)->first();
  
        if (!$sistema || !$sistema->formula_calculo) {
            throw new \Exception("Fórmula no encontrada para el sistema de préstamos ID: $idSistemaPrest");
        }
    
        // Generar la tabla de amortización
        return $this->calculaTablaAmortizacion(
            $request,
            $valorpres,
            $porcint,
            $id_periodo_pago,
            $numcuotas,
            $sistema->formula_calculo
        );
    }

    function calculaTablaAmortizacion($request, $valorpres, $porcint, $id_periodo_pago, $numcuotas, $formula) {
        $fec_inicial = $request->get('fec_inicial');
    
        // Validar fecha inicial
        if (!$fec_inicial || !strtotime($fec_inicial)) {
            throw new Exception("La fecha inicial no es válida.");
        }
    
        $date = new \DateTime($fec_inicial); // Fecha inicial como objeto DateTime
        $tabla = [];
        $saldo = $valorpres; // Saldo inicial del préstamo
    
        // Determinar la tasa efectiva según el período de pago
        switch ($id_periodo_pago) {
            case 1: // Diario
                $tem = ($porcint / 100) / 360;
                break;
            case 2: // Semanal
                $tem = ($porcint / 100) / 52;
                break;
            case 3: // Quincenal
                $tem = ($porcint / 100) / 24;
                break;
            case 4: // Mensual
                $tem = ($porcint / 100) / 12;
                break;
            case 5: // Anual
                $tem = ($porcint / 100);
                break;
            default:
                throw new Exception("Período de pago no válido.");
        }
    
        for ($x = 1; $x <= $numcuotas; $x++) {
            // Generar la fecha de la cuota actual
            $cuotaFecha = $this->adicionarFechas($date, $id_periodo_pago);
    
            // Parámetros disponibles para la fórmula
            $indice = $x;
    
            // Evaluar la fórmula dinámica
            eval("\$resultado = $formula;");
    
            // Normalizar campos para evitar problemas
            $resultado = array_merge([
                'indice' => $indice,
                'fecha' => $cuotaFecha,
                'interes' => 0,
                'amortizacion' => 0,
                'cfija_mensual' => 0,
                't_pagomes' => 0,
            ], $resultado);
    
            // Actualizar saldo restante
            $saldo = max(0, round($saldo - $resultado['amortizacion'], 2));
            $resultado['saldo'] = $saldo;
    
            // Agregar fila a la tabla
            $tabla['tabla'][] = array_merge($resultado, [
                'fecha_cuota_descrpcion' => $this->SpanishDate(strtotime($cuotaFecha)),
            ]);
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

    function adicionarFechas($date, $id_periodo_pago) {
        switch ($id_periodo_pago) {
            case 1: // Diario
                $date->add(new \DateInterval('P1D'));
                break;
            case 2: // Semanal
                $date->add(new \DateInterval('P7D'));
                break;
            case 3: // Quincenal
                $date->add(new \DateInterval('P15D')); // Sumar 15 días
                break;
            case 4: // Mensual
                $date->add(new \DateInterval('P1M'));
                break;
            case 5: // Anual
                $date->add(new \DateInterval('P1Y'));
                break;
            default:
                throw new Exception("Período de pago no válido. ID recibido: $id_periodo_pago");
        }
        return $date->format('Y-m-d');
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