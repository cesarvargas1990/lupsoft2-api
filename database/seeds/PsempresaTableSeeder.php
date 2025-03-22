<?php

use Illuminate\Database\Seeder;

class PsempresaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('psempresa')->insert([
            'nombre' => 'Empresa de pruebas',
			'nitempresa' => '1',
            'vlr_capinicial'=>'1500000',
            'email'=>'demo@email.com',
            'pagina'=>'https://credisoft.co',
            'telefono'=>'3184469889',
            'ciudad'=>'Cali - Valle del cauca',
            'ddirec'=>'Calle xx # yy-zz'

        ]);
    }
}
