<?php

use Illuminate\Database\Seeder;

class PsmenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		

         DB::table('psmenu')->insert([
            'orden' => '1',
            'nombre' => 'Principal',
            'ruta' => 'dashboard',
			'icono' => 'dashboard',
			'id_perfil' => 1,
			'ind_activo' => 1,
			'nitempresa' => 1
        ]);
		
		DB::table('psmenu')->insert([
            'orden' => '10',
            'nombre' => 'Salir',
            'ruta' => 'logout',
			'icono' => 'logout',
			'id_perfil' => 1,
			'ind_activo' => 1,
			'nitempresa' => 1
        ]);
		
		DB::table('psmenu')->insert([
            'orden' => '2',
            'nombre' => 'Clientes',
            'ruta' => 'clientes',
			'icono' => 'person',
			'id_perfil' => 1,
			'ind_activo' => 1,
			'nitempresa' => 1
        ]);
		
				DB::table('psmenu')->insert([
					'orden' => '1',
					'nombre' => 'Crear',
					'ruta' => 'clientes/crear',
					'icono' => 'add',
					'id_perfil' => 1,
					'ind_activo' => 1,
					'id_mpadre' => 3,
					'nitempresa' => 1
				]);
				
				DB::table('psmenu')->insert([
					'orden' => '2',
					'nombre' => 'Listar',
					'ruta' => 'clientes/listar',
					'icono' => 'list',
					'id_perfil' => 1,
					'ind_activo' => 1,
					'id_mpadre' => 3,
					'nitempresa' => 1
				]);
				
				DB::table('psmenu')->insert([
					'orden' => '2',
					'nombre' => 'Crear Prestamo',
					'ruta' => 'clientes/crearPrestamo',
					'icono' => 'add',
					'id_perfil' => 1,
					'ind_activo' => 1,
					'id_mpadre' => 3,
					'nitempresa' => 1
				]);
			
			
	   DB::table('psmenu')->insert([
            'orden' => '3',
            'nombre' => 'Parametros',
            'ruta' => 'parametros',
			'icono' => 'settings',
			'id_perfil' => 1,
			'ind_activo' => 1,
			'nitempresa' => 1
        ]);
		
				
				DB::table('psmenu')->insert([
					'orden' => '1',
					'nombre' => 'Formas de pago',
					'ruta' => 'parametros/formaspago',
					'icono' => 'playlist_add',
					'id_perfil' => 1,
					'id_mpadre' => 7,
					'ind_activo' => 1,
					'nitempresa' => 1
				]);

				DB::table('psmenu')->insert([
					'orden' => '2',
					'nombre' => 'Documentos',
					'ruta' => 'parametros/documentos',
					'icono' => 'file_copy',
					'id_perfil' => 1,
					'id_mpadre' => 7,
					'ind_activo' => 1,
					'nitempresa' => 1
				]);
				
				DB::table('psmenu')->insert([
					'orden' => '3',
					'nombre' => 'Empresa',
					'ruta' => 'parametros/empresa',
					'icono' => 'business',
					'id_perfil' => 1,
					'id_mpadre' => 7,
					'ind_activo' => 1,
					'nitempresa' => 1
				]);

		
    }
}
