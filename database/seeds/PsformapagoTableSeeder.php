<?php

use Illuminate\Database\Seeder;

class PsformapagoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('psformapago')->insert([
            
            'id_periodo_pago' => 1,
			'nomfpago' => 'Diario',
            'nitempresa' => '12345',
            'ind_solicporcint' => 1,
            'porcint' => 10,
            'numcuotas'=>20,
            'ind_solinumc' => 1,
			'codtipsistemap' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            
            'id_periodo_pago' => 2,
			'nomfpago' => 'Semanal',
            'nitempresa' => '12345',
            'ind_solicporcint' => 1,
            'porcint' => 10,
            'numcuotas'=>15,
            'ind_solinumc' => 1,
			'codtipsistemap' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            
            'id_periodo_pago' => 3,
			'nomfpago' => 'Quincenal',
            'nitempresa' => '12345',
            'ind_solicporcint' => 1,
            'porcint' => 10,
            'numcuotas'=>2,
            'ind_solinumc' => 0,
			'codtipsistemap' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            
            'id_periodo_pago' => 4,
			'nomfpago' => 'Mensual',
            'nitempresa' => '12345',
            'ind_solicporcint' => 1,
            'porcint' => 10,
            'numcuotas'=>12,
            'ind_solinumc' => 1,
			'codtipsistemap' => 1

        ]);
		
		DB::table('psformapago')->insert([
            
            'id_periodo_pago' => 5,
			'nomfpago' => 'Anual',
            'nitempresa' => '12345',
            'ind_solicporcint' => 1,
            'porcint' => 10,
            'numcuotas'=>2,
            'ind_solinumc' => 0,
			'codtipsistemap' => 1


        ]);
    }
}
