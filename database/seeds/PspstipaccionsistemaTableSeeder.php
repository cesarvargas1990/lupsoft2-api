<?php

use Illuminate\Database\Seeder;

class PstipaccionSistemaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		

         DB::table('pstipaccionsistema')->insert([
            'id' => '1',
            'nomtipaccionsist' => 'Creación de cliente',
			'desctipaccionsist' => 'Cargar los archivos, en el momento de la creación del cliente'
			

        ]);
		
		DB::table('pstipaccionsistema')->insert([
            'id' => '2',
            'nomtipaccionsist' => 'Creación de prestamo',
			'desctipaccionsist' => 'Generar Documentos cuando se termina de crear un prestamo'

        ]);
		
		DB::table('pstipaccionsistema')->insert([
            'id' => '3',
            'nomtipaccionsist' => 'Resultado pago prestamo',
			'desctipaccionsist' => 'Momento posterior al pago de la cuota de un prestamo'
			

        ]);
	

    }
}
