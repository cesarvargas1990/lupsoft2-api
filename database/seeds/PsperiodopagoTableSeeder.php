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
            'id_empresa' => '1'
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Semanal',
            'id_empresa' => '1'
          

        ]);
		
		
		DB::table('psperiodopago')->insert([
           
			'nomperiodopago' => 'Quincenal',
            'id_empresa' => '1'
            

        ]);
		
		
		DB::table('psperiodopago')->insert([
            'nomperiodopago' => 'Mensual',
            'id_empresa' => '1'
		

        ]);
		
		DB::table('psperiodopago')->insert([
          
			'nomperiodopago' => 'Anual',
            'id_empresa' => '1'
           


        ]);
    }
}
