<?php

use Illuminate\Database\Seeder;

class PstiposistemaprestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		


         DB::table('pstiposistemaprest')->insert([
            
            
            'codtipsistemap' => '1',
            'nomtipsistemap' => 'Sistema Frances',

        ]);
		
		DB::table('pstiposistemaprest')->insert([
            
            
            'codtipsistemap' => '2',
            'nomtipsistemap' => 'Sistema Ingles',

        ]);
		
		
		
		
		
    }
}
