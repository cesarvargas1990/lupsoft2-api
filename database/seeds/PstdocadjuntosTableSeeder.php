<?php

use Illuminate\Database\Seeder;

class PstdocadjuntosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('pstdocadjuntos')->insert([
            
           
			'nombre' => 'Cedula ciudadania',
            'nitempresa' => '12345',
			'idtipaccionsist' => 1
            
        ]);

        DB::table('pstdocadjuntos')->insert([
            
           
			'nombre' => 'Foto',
            'nitempresa' => '12345',
			'idtipaccionsist' => 1
            
        ]);
		
	
    }
}
