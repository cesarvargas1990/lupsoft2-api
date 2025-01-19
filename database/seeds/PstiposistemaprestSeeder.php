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
            'formula_calculo' => '$cfija_mensual = round(($valorpres * ($tem) / (1 - pow((1 + $tem), (-$numcuotas)))), 2); $interes = round($saldo * $tem, 2); $amortizacion = round($cfija_mensual - $interes, 2); $fecha = date(\'Y-m-d\', strtotime("+$indice months", strtotime(\'$fec_inicial\'))); $resultado = [\'indice\' => $indice, \'fecha\' => $fecha, \'interes\' => $interes, \'amortizacion\' => $amortizacion, \'cfija_mensual\' => $cfija_mensual, \'t_pagomes\' => $cfija_mensual ];'

        ]);
		
		DB::table('pstiposistemaprest')->insert([
            
            'codtipsistemap' => '2',
            'nomtipsistemap' => 'Sistema Ingles',
            'formula_calculo' => '$interes = round($valorpres * $tem, 2); $amortizacion = ($indice < $numcuotas) ? 0 : $valorpres; $fecha = date(\'Y-m-d\', strtotime("+$indice months", strtotime(\'$fec_inicial\'))); $resultado = [\'indice\' => $indice, \'fecha\' => $fecha, \'interes\' => $interes, \'amortizacion\' => $amortizacion, \'cfija_mensual\' => ($indice < $numcuotas) ? 0 : $amortizacion, \'t_pagomes\' => $interes + $amortizacion ];'

        ]);
		
		
		
    }
}
