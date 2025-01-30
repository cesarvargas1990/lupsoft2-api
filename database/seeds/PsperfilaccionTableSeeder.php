<?php

use Illuminate\Database\Seeder;

class PsperfilaccionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('psperfilaccion')->insert([
            'id_perfil' => '1',
            'nom_accion' => 'dashboard.imprimir_documentos'
        ]); 
    }
}
