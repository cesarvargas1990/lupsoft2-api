<?php

use Illuminate\Database\Seeder;

class PsusperfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
	

         DB::table('psusperfil')->insert([
            'id_user' => 1,
            'id_perfil' => 1,
            'ind_activo' => 1,
			'nitempresa' => '12345'
        ]);
		
		
    }
}
