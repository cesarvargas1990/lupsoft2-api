<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => app('hash')->make('password'),
             'is_admin' => 1,
             'nitempresa' => 1,
			 'ind_activo' => 1,
			 'id_cobrador' => 1
        ]);
    }
}
