<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		
		$this->call('UsersTableSeeder');
		$this->call('PsperfilTableSeeder');
		$this->call('PsempresaTableSeeder');
		$this->call('PsmenuTableSeeder');
		$this->call('PsusperfilTableSeeder');
		$this->call('PstipodocidentiSeeder');
		$this->call('PsformapagoTableSeeder');
		



    }
}
