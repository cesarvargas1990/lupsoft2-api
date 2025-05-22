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
            'id_empresa' => '1',
            'ind_activo' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Cobrador 1',
            'email' => 'cobrador1@admin.com',
            'password' => app('hash')->make('password'),
            'is_admin' => 0,
            'id_empresa' => '1',
            'ind_activo' => 1,
            'id_user' => 1
        ]);
    }
}
