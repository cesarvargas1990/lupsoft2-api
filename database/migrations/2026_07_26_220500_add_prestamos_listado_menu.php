<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPrestamosListadoMenu extends Migration
{
    public function up()
    {
        $menusDashboard = DB::table('psmenu')
            ->where('ruta', 'dashboard')
            ->select('id_perfil', 'id_empresa')
            ->get();

        foreach ($menusDashboard as $menuDashboard) {
            $existe = DB::table('psmenu')
                ->where('ruta', 'prestamos/listar')
                ->where('id_perfil', $menuDashboard->id_perfil)
                ->where('id_empresa', $menuDashboard->id_empresa)
                ->exists();

            if (!$existe) {
                DB::table('psmenu')->insert([
                    'orden' => 3,
                    'nombre' => 'Préstamos',
                    'ruta' => 'prestamos/listar',
                    'icono' => 'account_balance',
                    'id_perfil' => $menuDashboard->id_perfil,
                    'ind_activo' => 1,
                    'id_empresa' => $menuDashboard->id_empresa,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        DB::table('psmenu')
            ->where('ruta', 'prestamos/listar')
            ->delete();
    }
}
