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
    $router->get('cobradores/{id}',  ['uses' => 'UserController@getUsers']);


    // REST FULL SERVICES FOR TABLE => psclientes
    $router->get('psclientes',  ['uses' => 'PsclientesController@showAllPsclientes']);
    $router->get('psclientes/{id}', ['uses' => 'PsclientesController@showOnePsclientes']);
    $router->post('psclientes', ['uses' => 'PsclientesController@create']);
    $router->put('psclientes/{id}', ['uses' => 'PsclientesController@update']);
    $router->delete('psclientes/{id}', ['uses' => 'PsclientesController@delete']);
    $router->get('listadoclientes/{id}',  ['uses' => 'PsclientesController@ShowPsclientes']); // combo listas

    // REST FULL SERVICES FOR TABLE => pstipodocidenti
    $router->get('pstipodocidenti',  ['uses' => 'PstipodocidentiController@showAllpstipodocidenti']);
    $router->get('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@ShowPstipodocidenti']);
    $router->post('pstipodocidenti', ['uses' => 'PstipodocidentiController@create']);
    $router->put('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@update']);
    $router->delete('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@delete']);


    // REST FULL SERVICES FOR TABLE => psformapago
    $router->get('psformapago',  ['uses' => 'PsformapagoController@showAllpsformapago']);
    $router->get('psformapago/{id}', ['uses' => 'PsformapagoController@ShowPsformapago']);
    $router->post('psformapago', ['uses' => 'PsformapagoController@create']);
    $router->put('psformapago/{id}', ['uses' => 'PsformapagoController@update']);
    $router->delete('psformapago/{id}', ['uses' => 'PsformapagoController@delete']);
    $router->get('listaformaspago/{nit_empresa}', ['uses' => 'PsformapagoController@ShowPsformapago']); // combo listas


    // REST FULL SERVICES FOR TABLE => psperiodospago
    $router->get('psperiodopago',  ['uses' => 'PsperiodopagoController@showAllpsperiodospago']);
    $router->get('psperiodopago/{id}', ['uses' => 'PsperiodopagoController@showOnePsperiodopago']);
    $router->post('psperiodopago', ['uses' => 'PsperiodopagoController@create']);
    $router->put('psperiodopago/{id}', ['uses' => 'PsperiodopagoController@update']);
    $router->delete('psperiodopago/{id}', ['uses' => 'PsperiodopagoController@delete']);
    $router->get('listaperiodopago', ['uses' => 'PsperiodopagoController@ShowPsperiodopago']);


    // REST FULL SERVICES FOR TABLE => pstdocadjuntos
    $router->get('pstdocadjuntos',  ['uses' => 'PstdocadjuntosController@showAllPstdocadjuntos']);
    $router->get('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@showOnePstdocadjuntos']);
    $router->post('pstdocadjuntos', ['uses' => 'PstdocadjuntosController@create']);
    $router->put('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@update']);
    $router->delete('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@delete']);
    $router->get('listatdocadjuntos', ['uses' => 'PsperiodopagoController@ShowPstdocadjuntos']);
   

    // COMPLEX QUERYS (selects of multiple tables, inner custom querys)

    $router->get('consultaFormaPago/{id}' , ['uses' => 'PsformapagoController@consultaFormaPago']);
    $router->post('consultaFormasPago' , ['uses' => 'PsformapagoController@consultaFormasPago']);
    $router->post('calcularCuotas', ['uses' => 'CuotasController@calcularCuotas']);
    $router->post('calcularCuotas2', ['uses' => 'CuotasController@calcularCuotas2']);
    $router->post('listadoPrestamos' , ['uses' => 'PrestamosController@listadoPrestamos']  );



    // COMPLEX PROCESS (procedures, multiples insert into table, bussiness logic etc.)

    $router->post('test' , ['uses' => 'PrestamosController@guardarPrestamo']);
    $router->post('guardarPrestamo' , ['uses' => 'PrestamosController@guardarPrestamo']);

    $router->post('guardarArchivoAdjunto' , ['uses' => 'GuardarArchivoController@guardarArchivoAdjunto']);


 

});
