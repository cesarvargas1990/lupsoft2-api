<?php

use Illuminate\Database\Seeder;

class PsclientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    
	{

         DB::table('psclientes')->insert([
			'nomcliente' => 'Cliente de Prueba',
			'codtipdocid' =>  '1',
			'numdocumento' => '12345678' ,
			'ciudad' =>  'Santiago de cali',
			'telefijo' => '0324556525',
			'celular' =>  '3184559635',
			'direcasa' =>  'Calle 73 1f-45 barrio gaitan',
			'diretrabajo' => 'Calle 73 1f-45 barrio gaitan' ,
			'ubicasa' => '',
			'ubictrabajo' =>'',
			'nitempresa' =>  1,
			'ref1' =>  'Nombre de alguna ref personal 1',
			'ref2' => 'Nombre de alguna ref personal 2',
			'id_cobrador' =>  1,
			'id_user' => 1
        ]);
		

    }
}
