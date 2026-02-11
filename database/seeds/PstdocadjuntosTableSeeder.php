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
            'id_empresa' => '1'

        ]);

        DB::table('pstdocadjuntos')->insert([


            'nombre' => 'Foto',
            'id_empresa' => '1'

        ]);
    }
}
