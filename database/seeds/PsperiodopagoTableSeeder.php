<?php

use Illuminate\Database\Seeder;

class PsperiodopagoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		

         DB::table('psperiodopago')->insert([
            
			'nomperiodopago' => 'Diario',
            'nitempresa' => '12345'
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Semanal',
            'nitempresa' => '12345'
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
           
			'nomperiodopago' => 'Quincenal',
            'nitempresa' => '12345'
            

        ]);
		
		
		DB::table('psperiodopago')->insert([
            'nomperiodopago' => 'Mensual',
            'nitempresa' => '12345'
		

        ]);
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Anual',
            'nitempresa' => '12345'
           


        ]);
    }
}
