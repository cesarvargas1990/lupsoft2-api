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
        // Lista de permisos
        $permisos = [
            ['id_perfil' => 1, 'nom_accion' => 'cliente.crear'],
            ['id_perfil' => 1, 'nom_accion' => 'cliente.editar'],
            ['id_perfil' => 1, 'nom_accion' => 'cliente.eliminar'],
            ['id_perfil' => 1, 'nom_accion' => 'cliente.listado_creditos'],
            ['id_perfil' => 1, 'nom_accion' => 'dashboard.totales'],
            ['id_perfil' => 2, 'nom_accion' => 'dashboard.totales_user'],
            ['id_perfil' => 1, 'nom_accion' => 'dashboard.crear_prestamo'],
            ['id_perfil' => 1, 'nom_accion' => 'dashboard.imprimir_documentos'],
            ['id_perfil' => 1, 'nom_accion' => 'dashboard.listado_cuotas'],
            ['id_perfil' => 2, 'nom_accion' => 'dashboard.listado_cuotas'],
            ['id_perfil' => 1, 'nom_accion' => 'dashboard.eliminar_prestamo'],
            ['id_perfil' => 1, 'nom_accion' => 'documento.crear'],
            ['id_perfil' => 1, 'nom_accion' => 'documento.editar'],
            ['id_perfil' => 1, 'nom_accion' => 'documento.eliminar'],
            ['id_perfil' => 1, 'nom_accion' => 'empresa.modificar_datos_empresa'],
        ];

        // Insertar los permisos en la tabla psperfilaccion
        DB::table('psperfilaccion')->insert($permisos);
    }
}
