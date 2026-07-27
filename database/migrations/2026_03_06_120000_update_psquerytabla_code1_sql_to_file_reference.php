<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePsquerytablaCode1SqlToFileReference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('psquerytabla')
            ->where('codigo', 1)
            ->update([
                'sql' => '@file:database/sql/qrt1.sql',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('psquerytabla')
            ->where('codigo', 1)
            ->update([
                'sql' => "SELECT 
                @rownum := @rownum + 1 AS numero_cuota,
                id,
                DATE_FORMAT(fecha_pago, '%d/%m/%Y') AS fecha_pago,
                valor_pagar
            FROM 
                psfechaspago, 
                (SELECT @rownum := 0) r
            WHERE 
                id_prestamo = {id_prestamo}
            ORDER BY 
                numero_cuota ASC",
            ]);
    }
}
