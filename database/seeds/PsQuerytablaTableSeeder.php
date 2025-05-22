<?php

use Illuminate\Database\Seeder;

class PsQuerytablaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('psquerytabla')->insert([
            'id' => 1,
            'codigo' => '1',
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
            'id_empresa' => '1'

        ]);
    }
}
