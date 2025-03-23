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
		
		$this->call('PsempresaTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('PsclientesTableSeeder');
		$this->call('PsperfilTableSeeder');
		$this->call('PsmenuTableSeeder');
		$this->call('PsusperfilTableSeeder');
		$this->call('PstipodocidentiTableSeeder');
		$this->call('PsperiodopagoTableSeeder');
		$this->call('PstdocadjuntosTableSeeder');
		$this->call('PstdocplantTableSeeder');
		$this->call('PstiposistemaprestSeeder');
		$this->call('PsperfilaccionTableSeeder');
		$this->call('PsQuerytablaTableSeeder');


    }
}
