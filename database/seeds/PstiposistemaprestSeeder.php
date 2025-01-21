<?php

use Illuminate\Database\Seeder;

class PstiposistemaprestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('pstiposistemaprest')->insert([
            
            
            'codtipsistemap' => '1',
            'nomtipsistemap' => 'Sistema Frances',
            'formula' => "// Configuraciones iniciales
        \$fec_inicial = \$request->get('fec_inicial');
        \$fecha = \$fec_inicial;
        \$date = new \\DateTime(\$fecha);
        \$fechas[1] = \$date->format('Y-m-d');
        \$capital[0] = \$valorpres;
        \$salestax = 0;
        \$dpayment = 0;

        // Tasa efectiva mensual
        \$tem = (\$porcint / 100) / 12;

        // Cálculo de la cuota fija mensual
        \$valorCuota = round(
            (\$valorpres * (\$salestax / 100 + 1) - \$dpayment) * (\$tem) / 
            (1 - pow((1 + \$tem), (-\$numcuotas))), 2
        );

        \$tabla = [];
        for (\$x = 1; \$x <= \$numcuotas; \$x++) {
            // Calcular la fecha de la cuota
            \$fechas[\$x] = \$this->adicionarFechas(\$date, \$id_periodo_pago);

            // Calcular los valores de interés, amortización y saldo
            \$int = round(\$capital[\$x - 1] * \$tem, 2); // Interés de la cuota
            \$amort = round(\$valorCuota - \$int, 2); // Amortización

            // Ajustar en la última cuota
            if (\$x == \$numcuotas) {
                \$amort = round(\$capital[\$x - 1], 2); // Amortización igual al saldo restante
                \$valorCuota = \$amort + \$int; // Ajustar la cuota para que coincida
                \$capital[\$x] = 0; // Saldo final en \$0
            } else {
                \$capital[\$x] = round(\$capital[\$x - 1] - \$amort, 2); // Reducir el saldo normalmente
            }

            // Calcular D. Final
            \$d_final = \$capital[\$x - 1] - \$amort;

            // Agregar fila a la tabla
            \$tabla['tabla'][] = array(
                'indice' => \$x,
                'fecha_cuota_descrpcion' => \$this->SpanishDate(strtotime(\$fechas[\$x])),
                'fecha' => \$fechas[\$x],
                'interes' => \$int,
                'amortizacion' => \$amort,
                'saldo' => \$capital[\$x],
                'cfija_mensual' => \$valorCuota,
                't_pagomes' => \$valorCuota
            );
        }

        // Agregar detalles del préstamo
        \$tabla['datosprestamo'] = array(
            'tem' => \$tem,
            'valor_cuota' => \$valorCuota
        );

        // Formatear tabla para salida
        \$tformato = [];
        foreach (\$tabla['tabla'] as \$dato) {
            \$tformato[] = array(
                'N° Cuota' => \$dato['indice'],
                'Fecha Cuota' => \$dato['fecha_cuota_descrpcion'],
                'Interes' => '\$ ' . number_format(\$dato['interes'], 2),
                'Amortizacion' => '\$ ' . number_format(\$dato['amortizacion'], 2),
                'Saldo' => '\$ ' . number_format(\$dato['saldo'], 2),
                'Total a pagar cuota' => '\$ ' . number_format(\$dato['t_pagomes'], 2)
            );
        }

        // Preparar salida
        \$salida['datosprestamo'] = \$tabla['datosprestamo'];
        \$salida['tabla'] = \$tabla['tabla'];
        \$salida['tabla_formato'] = \$tformato;

        return \$salida;"
            


        ]);
		
		DB::table('pstiposistemaprest')->insert([
            
            
            'codtipsistemap' => '2',
             'nomtipsistemap' => 'Sistema Ingles',
             'formula' => "// Configuraciones iniciales
             \$fec_inicial = \$request->get('fec_inicial');
             \$fecha = \$fec_inicial;
             \$date = new \\DateTime(\$fecha);
     
             // Tasa efectiva mensual
             \$tem = (\$porcint / 100) / 12;
     
             // Intereses fijos en cada cuota
             \$interesFijo = round(\$valorpres * \$tem, 2);
     
             \$tabla = [];
             for (\$x = 1; \$x <= \$numcuotas; \$x++) {
                 // Generar fechas de las cuotas
                 \$fechas[\$x] = \$this->adicionarFechas(\$date, \$id_periodo_pago);
                 if (\$x < \$numcuotas) {
                     // Cuotas intermedias: solo intereses
                     \$tabla['tabla'][] = array(
                         'indice' => \$x,
                         'fecha_cuota_descrpcion' => \$this->SpanishDate(strtotime(\$fechas[\$x])),
                         'fecha' => \$fechas[\$x],
                         'interes' => \$interesFijo,
                         'amortizacion' => 0,
                         'saldo' => \$valorpres,
                         'cfija_mensual' => 0,
                         't_pagomes' => \$interesFijo
                     );
                 } else {
                     // Última cuota: intereses + capital
                     \$tabla['tabla'][] = array(
                         'indice' => \$x,
                         'fecha_cuota_descrpcion' => \$this->SpanishDate(strtotime(\$fechas[\$x])),
                         'fecha' => \$fechas[\$x],
                         'interes' => \$interesFijo,
                         'amortizacion' => \$valorpres,
                         'saldo' => 0,
                         'cfija_mensual' => \$valorpres,
                         't_pagomes' => \$interesFijo + \$valorpres
                     );
                 }
             }
     
             \$tformato = [];
             foreach (\$tabla['tabla'] as \$dato) {
                 \$tformato[] = array(
                     'N° Cuota' => \$dato['indice'],
                     'Fecha Cuota' => \$dato['fecha_cuota_descrpcion'],
                     'Interes' => '\$ ' . number_format(\$dato['interes'], 2),
                     'Capital' => '\$ ' . number_format(\$dato['amortizacion'], 2),
                     'Saldo' => '\$ ' . number_format(\$dato['saldo'], 2),
                     'Total a pagar cuota' => '\$ ' . number_format(\$dato['t_pagomes'], 2)
                 );
             }
     
             \$salida['tabla'] = \$tabla['tabla'];
             \$salida['tabla_formato'] = \$tformato;
     
             return \$salida;
         "

        ]);
		
		
		
		
		
    }
}
