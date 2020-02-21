<?php

use Illuminate\Database\Seeder;

class PsformapagoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('psformapago')->insert([
            'codfpago' => 1,
			'nomfpago' => 'Diario',
			'nitempresa' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            'codfpago' => 2,
			'nomfpago' => 'Semanal',
			'nitempresa' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            'codfpago' => 3,
			'nomfpago' => 'Quincenal',
			'nitempresa' => 1

        ]);
		
		
		DB::table('psformapago')->insert([
            'codfpago' => 4,
			'nomfpago' => 'Mensual',
			'nitempresa' => 1

        ]);
		
		DB::table('psformapago')->insert([
            'codfpago' => 5,
			'nomfpago' => 'Anual',
			'nitempresa' => 1

        ]);
    }
}
