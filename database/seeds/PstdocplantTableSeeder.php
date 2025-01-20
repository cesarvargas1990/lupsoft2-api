<?php

use Illuminate\Database\Seeder;

class PstdocplantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('pstdocplant')->insert([
            
            
            'nombre' => 'Contrato compraventa',
            'plantilla_html' => '',
            'nitempresa' => '12345',
			'idtipaccionsist' => 2

        ]);
		
		
	
		
		
	

    

        DB::table('pstdocplant')->insert([
            
            'nombre' => 'Pagare',
            'plantilla_html' => '',
            'nitempresa' => '12345',
			'idtipaccionsist' => 2


        ]);
		
		
    }
}
