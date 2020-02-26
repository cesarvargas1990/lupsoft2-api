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
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Semanal',
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
           
			'nomperiodopago' => 'Quincenal',
            

        ]);
		
		
		DB::table('psperiodopago')->insert([
            'nomperiodopago' => 'Mensual',
		

        ]);
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Anual',
           


        ]);
    }
}
