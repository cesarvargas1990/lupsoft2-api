INSERT INTO saequi_psofapi.pstiposistemaprest (codtipsistemap,nomtipsistemap,formula,created_at,updated_at) VALUES
	 (1,'Sistema Frances','// Configuraciones iniciales
$fec_inicial = $request->get(''fec_inicial'');
$date = new \\DateTime($fec_inicial);

// Tasa mensual ingresada
$tasaMensual = $porcint / 100;

// IDs reales de tu tabla periodo pago
$ID_DIARIO = 1;
$ID_SEMANAL = 2;
$ID_QUINCENAL = 3;
$ID_MENSUAL = 4;
$ID_ANUAL = 5;

// Calcular tasa por periodo de pago y meses reales
if ($id_periodo_pago == $ID_DIARIO) {
    $tasaPeriodo = $tasaMensual / 30;
    $meses = $numcuotas / 30;
} elseif ($id_periodo_pago == $ID_SEMANAL) {
    $tasaPeriodo = $tasaMensual / 4;
    $meses = $numcuotas / 4;
} elseif ($id_periodo_pago == $ID_QUINCENAL) {
    $tasaPeriodo = $tasaMensual / 2;
    $meses = $numcuotas / 2;
} elseif ($id_periodo_pago == $ID_MENSUAL) {
    $tasaPeriodo = $tasaMensual;
    $meses = $numcuotas;
} elseif ($id_periodo_pago == $ID_ANUAL) {
    $tasaPeriodo = $tasaMensual * 12;
    $meses = $numcuotas * 12;
} else {
    $tasaPeriodo = $tasaMensual;
    $meses = $numcuotas;
}

// Capital inicial SIN redondear para cálculo interno
$saldo = $valorpres;

// Cálculo cuota fija sistema francés
if ($tasaPeriodo == 0) {
    $valorCuota = $valorpres / $numcuotas;
} else {
    $valorCuota = $valorpres * $tasaPeriodo /
        (1 - pow(1 + $tasaPeriodo, -$numcuotas));
}

// Cuota que se muestra/paga normalmente
$valorCuotaRedondeada = round($valorCuota, 2);

$tabla = [];
$totalInteres = 0;
$totalCapital = 0;
$totalPagar = 0;
$fechas = [];

for ($x = 1; $x <= $numcuotas; $x++) {

    if ($x === 1) {
        $fechas[$x] = $date->format(''Y-m-d'');
    } else {
        $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
        $date = new \\DateTime($fechas[$x]);
    }

    // Cálculo interno sin redondear
    $interesReal = $saldo * $tasaPeriodo;
    $amortizacionReal = $valorCuota - $interesReal;

    // Ajuste última cuota
    if ($x == $numcuotas) {
        $interesReal = $saldo * $tasaPeriodo;
        $amortizacionReal = $saldo;
        $cuotaReal = $amortizacionReal + $interesReal;
        $saldo = 0;
    } else {
        $cuotaReal = $valorCuota;
        $saldo = $saldo - $amortizacionReal;
    }

    // Valores redondeados para guardar/mostrar
    $interes = round($interesReal, 2);
    $amortizacion = round($amortizacionReal, 2);
    $cuota = round($cuotaReal, 2);
    $saldoMostrar = round($saldo, 2);

    $totalInteres += $interesReal;
    $totalCapital += $amortizacionReal;
    $totalPagar += $cuotaReal;

    $tabla[''tabla''][] = [
        ''indice'' => $x,
        ''fecha_cuota_descrpcion'' => $this->spanishDate(strtotime($fechas[$x])),
        ''fecha'' => $fechas[$x],
        ''interes'' => $interes,
        ''amortizacion'' => $amortizacion,
        ''saldo'' => $saldoMostrar,
        ''cfija_mensual'' => $cuota,
        ''t_pagomes'' => $cuota
    ];
}

// Datos del préstamo
$tabla[''datosprestamo''] = [
    ''meses'' => $meses,
    ''tasa_periodo'' => $tasaPeriodo,
    ''valor_cuota'' => $valorCuotaRedondeada,
    ''total_interes'' => round($totalInteres, 2),
    ''total_capital'' => round($totalCapital, 2),
    ''total_pagar'' => round($totalPagar, 2)
];

// Formatear tabla para salida
$tformato = [];

foreach ($tabla[''tabla''] as $dato) {
    $tformato[] = [
        ''N° Cuota'' => $dato[''indice''],
        ''Fecha Cuota'' => $dato[''fecha_cuota_descrpcion''],
        ''Interes'' => ''$ '' . number_format($dato[''interes''], 2),
        ''Amortizacion'' => ''$ '' . number_format($dato[''amortizacion''], 2),
        ''Saldo'' => ''$ '' . number_format($dato[''saldo''], 2),
        ''Total a pagar cuota'' => ''$ '' . number_format($dato[''t_pagomes''], 2)
    ];
}

// Preparar salida
$salida[''datosprestamo''] = $tabla[''datosprestamo''];
$salida[''tabla''] = $tabla[''tabla''];
$salida[''tabla_formato''] = $tformato;

return $salida;',NULL,NULL),
	 (2,'Sistema Ingles (Capital al final)','// Configuraciones iniciales
$fec_inicial = $request->get(''fec_inicial'');
$date = new \\DateTime($fec_inicial);

// Tasa mensual ingresada
$tasaMensual = $porcint / 100;

// IDs reales de tu tabla periodo pago
$ID_DIARIO = 1;
$ID_SEMANAL = 2;
$ID_QUINCENAL = 3;
$ID_MENSUAL = 4;
$ID_ANUAL = 5;

// Calcular meses reales del préstamo
if ($id_periodo_pago == $ID_DIARIO) {
    $meses = $numcuotas / 30;
} elseif ($id_periodo_pago == $ID_SEMANAL) {
    $meses = $numcuotas / 4;
} elseif ($id_periodo_pago == $ID_QUINCENAL) {
    $meses = $numcuotas / 2;
} elseif ($id_periodo_pago == $ID_MENSUAL) {
    $meses = $numcuotas;
} elseif ($id_periodo_pago == $ID_ANUAL) {
    $meses = $numcuotas * 12;
} else {
    $meses = $numcuotas;
}

// Sistema inglés
// Durante la vida del préstamo solo se pagan intereses.
// El capital completo se paga en la última cuota.
$totalInteresReal = $valorpres * $tasaMensual * $meses;
$interesCuotaReal = $totalInteresReal / $numcuotas;

$tabla = [];
$tformato = [];
$fechas = [];

$totalInteres = 0;
$totalCapital = 0;
$totalPagar = 0;

for ($x = 1; $x <= $numcuotas; $x++) {

    // Fechas
    if ($x === 1) {
        $fechas[$x] = $date->format(''Y-m-d'');
    } else {
        $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
        $date = new \\DateTime($fechas[$x]);
    }

    // Interés de la cuota
    if ($x == $numcuotas) {
        // Ajuste final para que la suma de intereses cierre exacta
        $interes = round(
            $totalInteresReal - ($interesCuotaReal * ($numcuotas - 1)),
            2
        );

        $capital = round($valorpres, 2);
        $saldo = 0;
        $cuota = round($capital + $interes, 2);
    } else {
        $interes = round($interesCuotaReal, 2);
        $capital = 0;
        $saldo = round($valorpres, 2);
        $cuota = $interes;
    }

    // Totales sumando lo que ve el usuario
    $totalInteres = round($totalInteres + $interes, 2);
    $totalCapital = round($totalCapital + $capital, 2);
    $totalPagar = round($totalPagar + $cuota, 2);

    $fila = [
        ''indice'' => $x,
        ''fecha_cuota_descrpcion'' => $this->spanishDate(strtotime($fechas[$x])),
        ''fecha'' => $fechas[$x],
        ''interes'' => $interes,
        ''amortizacion'' => $capital,
        ''saldo'' => $saldo,
        ''cfija_mensual'' => $cuota,
        ''t_pagomes'' => $cuota
    ];

    $tabla[''tabla''][] = $fila;

    $tformato[] = [
        ''N° Cuota'' => $fila[''indice''],
        ''Fecha Cuota'' => $fila[''fecha_cuota_descrpcion''],
        ''Interes'' => ''$ '' . number_format($fila[''interes''], 2),
        ''Capital'' => ''$ '' . number_format($fila[''amortizacion''], 2),
        ''Saldo'' => ''$ '' . number_format($fila[''saldo''], 2),
        ''Total a pagar cuota'' => ''$ '' . number_format($fila[''t_pagomes''], 2)
    ];
}

// Datos del préstamo
$tabla[''datosprestamo''] = [
    ''meses'' => $meses,
    ''total_interes'' => $totalInteres,
    ''total_capital'' => $totalCapital,
    ''total_pagar'' => $totalPagar,
    ''valor_cuota_interes'' => round($interesCuotaReal, 2),
    ''valor_cuota_final'' => round($valorpres + round(
        $totalInteresReal - ($interesCuotaReal * ($numcuotas - 1)),
        2
    ), 2)
];

$salida[''datosprestamo''] = $tabla[''datosprestamo''];
$salida[''tabla''] = $tabla[''tabla''];
$salida[''tabla_formato''] = $tformato;

return $salida;',NULL,NULL),
	 (3,'Sistema Tradicional','// Configuraciones iniciales
$fec_inicial = $request->get(''fec_inicial'');
$date = new \\DateTime($fec_inicial);

// Tasa mensual ingresada
$tasaMensual = $porcint / 100;

// IDs reales de tu tabla periodo pago
$ID_DIARIO = 1;
$ID_SEMANAL = 2;
$ID_QUINCENAL = 3;
$ID_MENSUAL = 4;
$ID_ANUAL = 5;

// Calcular meses reales del préstamo
if ($id_periodo_pago == $ID_DIARIO) {
    $meses = $numcuotas / 30;
} elseif ($id_periodo_pago == $ID_SEMANAL) {
    $meses = $numcuotas / 4;
} elseif ($id_periodo_pago == $ID_QUINCENAL) {
    $meses = $numcuotas / 2;
} elseif ($id_periodo_pago == $ID_MENSUAL) {
    $meses = $numcuotas;
} elseif ($id_periodo_pago == $ID_ANUAL) {
    $meses = $numcuotas * 12;
} else {
    $meses = $numcuotas;
}

// Sistema tradicional / interés plano
// El interés total se calcula sobre el capital inicial,
// no sobre el saldo pendiente.
$totalInteresReal = $valorpres * $tasaMensual * $meses;
$totalPagarReal = $valorpres + $totalInteresReal;

$capitalCuotaReal = $valorpres / $numcuotas;
$interesCuotaReal = $totalInteresReal / $numcuotas;
$valorCuotaReal = $totalPagarReal / $numcuotas;

$saldoReal = $valorpres;

$tabla = [];
$tformato = [];
$fechas = [];

$totalInteres = 0;
$totalCapital = 0;
$totalPagar = 0;

for ($x = 1; $x <= $numcuotas; $x++) {

    // Fechas
    if ($x === 1) {
        $fechas[$x] = $date->format(''Y-m-d'');
    } else {
        $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
        $date = new \\DateTime($fechas[$x]);
    }

    if ($x == $numcuotas) {

        // Ajuste final para cerrar capital e intereses exactos
        $capital = round($valorpres - $totalCapital, 2);
        $interes = round($totalInteresReal - $totalInteres, 2);

        $cuota = round($capital + $interes, 2);
        $saldo = 0;

    } else {

        $capital = round($capitalCuotaReal, 2);
        $interes = round($interesCuotaReal, 2);
        $cuota = round($capital + $interes, 2);

        $saldoReal = $saldoReal - $capitalCuotaReal;
        $saldo = round($saldoReal, 2);
    }

    // Totales sumando lo que ve el usuario
    $totalCapital = round($totalCapital + $capital, 2);
    $totalInteres = round($totalInteres + $interes, 2);
    $totalPagar = round($totalPagar + $cuota, 2);

    $fila = [
        ''indice'' => $x,
        ''fecha_cuota_descrpcion'' => $this->spanishDate(strtotime($fechas[$x])),
        ''fecha'' => $fechas[$x],
        ''interes'' => $interes,
        ''amortizacion'' => $capital,
        ''saldo'' => $saldo,
        ''cfija_mensual'' => $cuota,
        ''t_pagomes'' => $cuota
    ];

    $tabla[''tabla''][] = $fila;

    $tformato[] = [
        ''N° Cuota'' => $fila[''indice''],
        ''Fecha Cuota'' => $fila[''fecha_cuota_descrpcion''],
        ''Interes'' => ''$ '' . number_format($fila[''interes''], 2),
        ''Capital'' => ''$ '' . number_format($fila[''amortizacion''], 2),
        ''Saldo'' => ''$ '' . number_format($fila[''saldo''], 2),
        ''Total a pagar cuota'' => ''$ '' . number_format($fila[''t_pagomes''], 2)
    ];
}

// Datos del préstamo
$tabla[''datosprestamo''] = [
    ''meses'' => $meses,
    ''total_interes'' => $totalInteres,
    ''total_capital'' => $totalCapital,
    ''total_pagar'' => $totalPagar,
    ''valor_cuota'' => round($valorCuotaReal, 2),
    ''valor_capital_cuota'' => round($capitalCuotaReal, 2),
    ''valor_interes_cuota'' => round($interesCuotaReal, 2)
];

$salida[''datosprestamo''] = $tabla[''datosprestamo''];
$salida[''tabla''] = $tabla[''tabla''];
$salida[''tabla_formato''] = $tformato;

return $salida;',NULL,NULL),
	 (4,'Sistema Aleman','// Configuraciones iniciales
$fec_inicial = $request->get(''fec_inicial'');
$date = new \\DateTime($fec_inicial);

// Tasa mensual ingresada
$tasaMensual = $porcint / 100;

// IDs reales de tu tabla periodo pago
$ID_DIARIO = 1;
$ID_SEMANAL = 2;
$ID_QUINCENAL = 3;
$ID_MENSUAL = 4;
$ID_ANUAL = 5;

// Calcular tasa por periodo y meses reales
if ($id_periodo_pago == $ID_DIARIO) {
    $tasaPeriodo = $tasaMensual / 30;
    $meses = $numcuotas / 30;
} elseif ($id_periodo_pago == $ID_SEMANAL) {
    $tasaPeriodo = $tasaMensual / 4;
    $meses = $numcuotas / 4;
} elseif ($id_periodo_pago == $ID_QUINCENAL) {
    $tasaPeriodo = $tasaMensual / 2;
    $meses = $numcuotas / 2;
} elseif ($id_periodo_pago == $ID_MENSUAL) {
    $tasaPeriodo = $tasaMensual;
    $meses = $numcuotas;
} elseif ($id_periodo_pago == $ID_ANUAL) {
    $tasaPeriodo = $tasaMensual * 12;
    $meses = $numcuotas * 12;
} else {
    $tasaPeriodo = $tasaMensual;
    $meses = $numcuotas;
}

// Capital amortizado en cada cuota (constante)
$capitalCuotaReal = $valorpres / $numcuotas;

$saldoReal = $valorpres;

$totalInteres = 0;
$totalCapital = 0;
$totalPagar = 0;

$tabla = [];
$tformato = [];
$fechas = [];

for ($x = 1; $x <= $numcuotas; $x++) {

    // Fechas
    if ($x === 1) {
        $fechas[$x] = $date->format(''Y-m-d'');
    } else {
        $fechas[$x] = $this->adicionarFechas($date, $id_periodo_pago);
        $date = new \\DateTime($fechas[$x]);
    }

    // Interés sobre saldo pendiente
    $interesReal = $saldoReal * $tasaPeriodo;

    // Ajuste última cuota
    if ($x == $numcuotas) {
        $capitalReal = $saldoReal;
    } else {
        $capitalReal = $capitalCuotaReal;
    }

    $cuotaReal = $capitalReal + $interesReal;

    // Actualizar saldo
    $saldoReal -= $capitalReal;

    if ($saldoReal < 0.01) {
        $saldoReal = 0;
    }

    // Valores mostrados
    $interes = round($interesReal, 2);
    $capital = round($capitalReal, 2);
    $cuota = round($cuotaReal, 2);
    $saldo = round($saldoReal, 2);

    // Totales comerciales
    $totalInteres += $interes;
    $totalCapital += $capital;
    $totalPagar += $cuota;

    $tabla[''tabla''][] = [
        ''indice'' => $x,
        ''fecha_cuota_descrpcion'' => $this->spanishDate(strtotime($fechas[$x])),
        ''fecha'' => $fechas[$x],
        ''interes'' => $interes,
        ''amortizacion'' => $capital,
        ''saldo'' => $saldo,
        ''cfija_mensual'' => $cuota,
        ''t_pagomes'' => $cuota
    ];

    $tformato[] = [
        ''N° Cuota'' => $x,
        ''Fecha Cuota'' => $this->spanishDate(strtotime($fechas[$x])),
        ''Interes'' => ''$ '' . number_format($interes, 2),
        ''Amortizacion'' => ''$ '' . number_format($capital, 2),
        ''Saldo'' => ''$ '' . number_format($saldo, 2),
        ''Total a pagar cuota'' => ''$ '' . number_format($cuota, 2)
    ];
}

// Datos del préstamo
$tabla[''datosprestamo''] = [
    ''meses'' => $meses,
    ''tasa_periodo'' => $tasaPeriodo,
    ''valor_capital_cuota'' => round($capitalCuotaReal, 2),
    ''total_interes'' => round($totalInteres, 2),
    ''total_capital'' => round($totalCapital, 2),
    ''total_pagar'' => round($totalPagar, 2)
];

$salida[''datosprestamo''] = $tabla[''datosprestamo''];
$salida[''tabla''] = $tabla[''tabla''];
$salida[''tabla_formato''] = $tformato;

return $salida;',NULL,NULL);
