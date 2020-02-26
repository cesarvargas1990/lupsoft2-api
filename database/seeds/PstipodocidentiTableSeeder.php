<?php

use Illuminate\Database\Seeder;

class PstipodocidentiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		

         DB::table('pstipodocidenti')->insert([
		 
            'codtipdocid' => 1,
            'nomtipodocumento' => 'Cédula',
			'nitempresa' => 1
        ]);
		
		  DB::table('pstipodocidenti')->insert([
		 
            'codtipdocid' => 2,
            'nomtipodocumento' => 'Nit',
			'nitempresa' => 1
        ]);
		
		
    }
}
