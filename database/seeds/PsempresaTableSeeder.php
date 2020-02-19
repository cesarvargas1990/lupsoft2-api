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
            'nombre' => 'Empresa pruebas',
			'nitempresa' => 1

        ]);
    }
}
