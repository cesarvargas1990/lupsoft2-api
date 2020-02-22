<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// API route group
$router->group(['prefix' => 'api'], function () use ($router) {

    // AUTH SERVICES
    // Matches "/api/register
   $router->post('auth/register', 'AuthController@register');
     // Matches "/api/login
    $router->post('auth/login', 'AuthController@login');
    // Matches "/api/profile
    $router->get('profile', 'UserController@profile');
    // Matches "/api/user
    //get one user by id
    $router->get('users/{id}', 'UserController@singleUser');
    // Matches "/api/users
    $router->get('users', 'UserController@allUsers');


   // REST FULL SERVICES FOR TABLE => psclientes
   $router->get('psclientes',  ['uses' => 'PsclientesController@showAllPsclientes']);
   $router->get('psclientes/{id}', ['uses' => 'PsclientesController@showOnePsclientes']);
   $router->post('psclientes', ['uses' => 'PsclientesController@create']);
   $router->put('psclientes/{id}', ['uses' => 'PsclientesController@update']);
   $router->delete('psclientes/{id}', ['uses' => 'PsclientesController@delete']);

   // REST FULL SERVICES FOR TABLE => pstipodocidenti
   $router->get('pstipodocidenti',  ['uses' => 'PstipodocidentiController@showAllpstipodocidenti']);
   $router->get('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@ShowPstipodocidenti']);
   $router->post('pstipodocidenti', ['uses' => 'PstipodocidentiController@create']);
   $router->put('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@update']);
   $router->delete('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@delete']);

   // SOME SERVICES

   $router->get('cobradores/{id}',  ['uses' => 'UserController@getUsers']);
   $router->get('listadoclientes/{id}',  ['uses' => 'PsclientesController@ShowPsclientes']);
   $router->post('calcularCuotas', ['uses' => 'CuotasController@calcularCuotas']);
    $router->get('listaformaspago/{nit_empresa}', ['uses' => 'PsformaspagoController@ShowPsformapago']);

    $router->post('calcularCuotas2', ['uses' => 'CuotasController@calcularCuotas2']);

    $router->post('guardarPrestamo' , ['uses' => 'PrestamosController@guardarPrestamo']);



    $router->post('test' , ['uses' => 'PrestamosController@guardarPrestamo']);




});
