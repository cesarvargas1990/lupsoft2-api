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



use Illuminate\Http\Request;

// Definir constantes para rutas repetitivas
define('PSCLIENTES_ROUTE', 'psclientes/{id}');
define('PSDOCADJUNTOS_ROUTE', 'psdocadjuntos/{id}');
define('PSFORMAPAGO_ROUTE', 'psformapago/{id}');
define('PSTIPOSISTEMAPREST_ROUTE', 'pstiposistemaprest/{id}');
define('PSEMPRESA_ROUTE', 'psempresa/{id}');
define('PSPERIODO_PAGO_ROUTE', 'psperiodopago/{id}');
define('PSPAGOS_ROUTE', 'pspagos/{id}');
define('PSFECHASPAGO_ROUTE', 'psfechaspago/{id}');


$router->get('/upload/documentosAdjuntos/{filepath:.*}', function (Request $request, $filepath) use ($router) {
    dd("Ruta alcanzada: $filepath");
    $file = storage_path("app/$filepath");

    if (file_exists($file)) {
        return response()->download($file);
    }

    return response()->json(['error' => 'File not found'], 404);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API route group
$router->group(['prefix' => ''], function () use ($router) {

    // AUTH SERVICES
    // Matches "/api/register
   $router->post('auth/register', 'AuthController@register');
     // Matches "/api/login
    $router->post('auth/login', 'AuthController@login');
    $router->post('auth/logout', 'AuthController@logout');
    // Matches "/api/profile
    $router->get('profile', 'UserController@profile');
    // Matches "/api/user
    //get one user by id
    $router->get('users/{id}', 'UserController@singleUser');
    // Matches "/api/users
    $router->get('users', 'UserController@allUsers');
    $router->get('cobradores/{id}',  ['uses' => 'UserController@getUsers']);

  
    // REST FULL SERVICES FOR TABLE => psclientes
    $router->post('psclientes/{nitempresa}', ['uses' => 'PsclientesController@showAllPsclientes']);
    $router->get(PSCLIENTES_ROUTE, ['uses' => 'PsclientesController@showOnePsclientes']);
    $router->post('psclientes', ['uses' => 'PsclientesController@create']);
    $router->put(PSCLIENTES_ROUTE, ['uses' => 'PsclientesController@update']);
    $router->delete(PSCLIENTES_ROUTE, ['uses' => 'PsclientesController@delete']);
    $router->get('listadoclientes/{id}', ['uses' => 'PsclientesController@ShowPsclientes']);

    // REST FULL SERVICES FOR TABLE => pstipodocidenti
    $router->get('pstipodocidenti', ['uses' => 'PstipodocidentiController@ShowPstipodocidenti']);
    $router->post('pstipodocidenti', ['uses' => 'PstipodocidentiController@create']);
    $router->put('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@update']);
    $router->delete('pstipodocidenti/{id}', ['uses' => 'PstipodocidentiController@delete']);


    // REST FULL SERVICES FOR TABLE => psformapago
    $router->get('psformapago', ['uses' => 'PsformapagoController@showAllpsformapago']);
    $router->get(PSFORMAPAGO_ROUTE, ['uses' => 'PsformapagoController@ShowPsformapago']);
    $router->post('psformapago', ['uses' => 'PsformapagoController@create']);
    $router->put(PSFORMAPAGO_ROUTE, ['uses' => 'PsformapagoController@update']);
    $router->delete(PSFORMAPAGO_ROUTE, ['uses' => 'PsformapagoController@delete']);
    $router->get('listaformaspago/{nit_empresa}', ['uses' => 'PsformapagoController@ShowPsformapago']);

    // REST FULL SERVICES FOR TABLE => psperiodospago
    $router->get('psperiodopago', ['uses' => 'PsperiodopagoController@showAllpsperiodospago']);
    $router->get(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@showOnePsperiodopago']);
    $router->post('psperiodopago', ['uses' => 'PsperiodopagoController@create']);
    $router->put(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@update']);
    $router->delete(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@delete']);
    $router->get('listaperiodopago', ['uses' => 'PsperiodopagoController@ShowPsperiodopago']);


    // REST FULL SERVICES FOR TABLE => pstdocadjuntos
    $router->get('pstdocadjuntos',  ['uses' => 'PstdocadjuntosController@showAllPstdocadjuntos']);
    $router->get('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@showOnePstdocadjuntos']);
    $router->post('pstdocadjuntos', ['uses' => 'PstdocadjuntosController@create']);
    $router->put('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@update']);
    $router->delete('pstdocadjuntos/{id}', ['uses' => 'PstdocadjuntosController@delete']);
    $router->get('listatdocadjuntos/{nitempresa}', ['uses' => 'PstdocadjuntosController@ShowPstdocadjuntos']);


    // REST FULL SERVICES FOR TABLE => pstdocplant
    $router->get('pstdocplant',  ['uses' => 'PstdocplantController@showAllpstdocplant']);
    $router->get('pstdocplant/{id}', ['uses' => 'PstdocplantController@ShowPstdocplant']);
    $router->post('pstdocplant', ['uses' => 'PstdocplantController@create']);
    $router->put('pstdocplant/{id}', ['uses' => 'PstdocplantController@update']);
    $router->delete('pstdocplant/{id}', ['uses' => 'PstdocplantController@delete']);

 
    // REST FULL SERVICES FOR TABLE => pspagos
    $router->get('pspagos',  ['uses' => 'PspagosController@showAllPspagos']);
    $router->post('pspagos', ['uses' => 'PspagosController@create']);
    $router->put(PSPAGOS_ROUTE, ['uses' => 'PspagosController@update']);
    $router->delete(PSPAGOS_ROUTE, ['uses' => 'PspagosController@delete']);

    // REST FULL SERVICES FOR TABLE => psfechaspago
    $router->get('psfechaspago/{id_prestamo}',  ['uses' => 'PsfechaspagoController@showAllPsfechaspago']);
    $router->post('psfechaspago', ['uses' => 'PsfechaspagoController@create']);
    $router->put('psfechaspago/{id}', ['uses' => 'PsfechaspagoController@update']);
    $router->delete('psfechaspago/{id}', ['uses' => 'PsfechaspagoController@delete']);


    // REST FULL SERVICES FOR TABLE => psdocadjuntos


    $router->get('psdocadjuntos',  ['uses' => 'PsdocadjuntosController@showAllPstdocadjuntos']);
    $router->get('psdocadjuntos/{id}', ['uses' => 'PsdocadjuntosController@showOnePsdocadjuntos']);
    $router->post('psdocadjuntos', ['uses' => 'PsdocadjuntosController@create']);
    $router->put('psdocadjuntos/{id}', ['uses' => 'PsdocadjuntosController@update']);
    $router->delete('psdocadjuntos/{id}', ['uses' => 'PsdocadjuntosController@delete']);



     // REST FULL SERVICES FOR TABLE => PspstiposistemaprestController
     $router->get('pstiposistemaprest',  ['uses' => 'PspstiposistemaprestController@showAll']);
     $router->get('pstiposistemaprest/{id}', ['uses' => 'PspstiposistemaprestController@Show']);
     $router->post('pstiposistemaprest', ['uses' => 'PspstiposistemaprestController@create']);
     $router->put('pstiposistemaprest/{id}', ['uses' => 'PspstiposistemaprestController@update']);
     $router->delete('pstiposistemaprest/{id}', ['uses' => 'PspstiposistemaprestController@delete']);
     $router->get('listatiposistemaprest/', ['uses' => 'PspstiposistemaprestController@list']); // combo listas


	 
	  
	 // REST FULL SERVICES FOR TABLE => psempresa
    
    $router->get('psempresa/{id}', ['uses' => 'PsempresaController@showOnePsempresa']);
    $router->put('psempresa/{id}', ['uses' => 'PsempresaController@update']);
	

    // COMPLEX QUERYS (selects of multiple tables, inner custom querys)

    $router->post('consultaTipoDocPlantilla' , ['uses' => 'PsformapagoController@consultaTipoDocPlantilla']);
    $router->post('calcularCuotas', ['uses' => 'CuotasController@calcularCuotas']);
    $router->post('calcularCuotas2', ['uses' => 'CuotasController@calcularCuotas2']);
    $router->post('listadoPrestamos' , ['uses' => 'PrestamosController@listadoPrestamos']  );
    $router->post('prestamosCliente', ['uses' => 'PrestamosController@prestamosCliente']);
    $router->post('renderTemplates', ['uses' => 'PrestamosController@getPlantillasDocumentosPrestamo']);

 
 
    // COMPLEX PROCESS (procedures, multiples insert into table, bussiness logic etc.)

    
    $router->post('guardarPrestamo' , ['uses' => 'PrestamosController@guardarPrestamo']);
    $router->get('generarVariablesPlantillas/{nit_empresa}' , ['uses' => 'PrestamosController@generarVariablesPlantillas']);
    $router->post('guardarArchivoAdjunto' , ['uses' => 'GuardarArchivoController@guardarArchivoAdjunto']);
    $router->put('editarArchivoAdjunto' , ['uses' => 'GuardarArchivoController@editarArchivoAdjunto']);
    $router->delete('eliminarPrestamo/{id_prestamo}' , ['uses' => 'PrestamosController@eliminarPrestamo']);
    $router->get('capitalprestado/{nit_empresa}',['uses'=>'PrestamosController@totalcapital']);
    $router->post('totalprestadohoy',['uses'=>'PrestamosController@totalprestadohoy']);
    $router->post('totalintereshoy',['uses'=>'PrestamosController@totalintereshoy']);
    $router->post('totalinteres',['uses'=>'PrestamosController@totalinteres']);
    $router->get('totalprestado/{nit_empresa}',['uses'=>'PrestamosController@totalprestado']);
    $router->post('totales_dashboard',['uses'=>'PrestamosController@totales_dashboard']);



    


});
