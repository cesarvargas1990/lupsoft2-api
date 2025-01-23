<?php

use Illuminate\Database\Seeder;

class PsperfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('psperfil')->insert([
            'nombre' => 'Administrador',
            'ind_activo' => 1
        ]);
        DB::table('psperfil')->insert([
            'nombre' => 'Cobrador',
            'ind_activo' => 1
        ]);
    }
}
