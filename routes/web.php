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
define('PSTIPOSISTEMAPREST_ROUTE', 'pstiposistemaprest/{id}');
define('PSEMPRESA_ROUTE', 'psempresa/{id}');
define('PSPERIODO_PAGO_ROUTE', 'psperiodopago/{id}');
define('PSPAGOS_ROUTE', 'pspagos/{id}');
define('PSFECHASPAGO_ROUTE', 'psfechaspago/{id}');
define('PSTDOCPLANT_ID', 'pstdocplant/{id}');
define('PSTDOCADJUNTOS_ID', 'pstdocadjuntos/{id}');
$router->get('/upload/documentosAdjuntos/{filepath:.*}', function ($filepath) {
    $safePath = str_replace('\\', '/', $filepath);
    if (strpos($safePath, '..') !== false) {
        return response()->json(['error' => 'Invalid path'], 400);
    }

    $basePath = base_path('upload/documentosAdjuntos');
    $file = $basePath . '/' . ltrim($safePath, '/');
    $realBasePath = realpath($basePath);
    $realFilePath = realpath($file);

    if ($realBasePath && $realFilePath && strpos($realFilePath, $realBasePath) === 0 && is_file($realFilePath)) {
        $mimeType = function_exists('mime_content_type') ? mime_content_type($realFilePath) : null;
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        return response(file_get_contents($realFilePath), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($realFilePath) . '"',
            'Content-Length' => filesize($realFilePath),
        ]);
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
    $router->get('cobradores/{id}', ['uses' => 'UserController@getUsers']);


    // REST FULL SERVICES FOR TABLE => psclientes
    $router->post('psclientes/{id_empresa}', ['uses' => 'PsclientesController@showAllPsclientes']);
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

    $router->get('listaformaspago/{id_empresa}', ['uses' => 'PsformapagoController@ShowPsformapago']);

    // REST FULL SERVICES FOR TABLE => psperiodospago
    $router->get('psperiodopago', ['uses' => 'PsperiodopagoController@showAllpsperiodospago']);
    $router->get(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@showOnePsperiodopago']);
    $router->post('psperiodopago', ['uses' => 'PsperiodopagoController@create']);
    $router->put(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@update']);
    $router->delete(PSPERIODO_PAGO_ROUTE, ['uses' => 'PsperiodopagoController@delete']);
    $router->get('listaperiodopago', ['uses' => 'PsperiodopagoController@ShowPsperiodopago']);


    // REST FULL SERVICES FOR TABLE => pstdocadjuntos
    $router->get('pstdocadjuntos', ['uses' => 'PstdocadjuntosController@showAllPstdocadjuntos']);
    $router->get(PSTDOCADJUNTOS_ID, ['uses' => 'PstdocadjuntosController@showOnePstdocadjuntos']);
    $router->post('pstdocadjuntos', ['uses' => 'PstdocadjuntosController@create']);
    $router->put(PSTDOCADJUNTOS_ID, ['uses' => 'PstdocadjuntosController@update']);
    $router->delete(PSTDOCADJUNTOS_ID, ['uses' => 'PstdocadjuntosController@delete']);
    $router->get('listatdocadjuntos/{id_empresa}', ['uses' => 'PstdocadjuntosController@ShowPstdocadjuntos']);


    // REST FULL SERVICES FOR TABLE => pstdocplant
    $router->get('pstdocplant', ['uses' => 'PstdocplantController@showAllpstdocplant']);
    $router->get(PSTDOCPLANT_ID, ['uses' => 'PstdocplantController@ShowPstdocplant']);
    $router->post('pstdocplant', ['uses' => 'PstdocplantController@create']);
    $router->put(PSTDOCPLANT_ID, ['uses' => 'PstdocplantController@update']);
    $router->delete(PSTDOCPLANT_ID, ['uses' => 'PstdocplantController@delete']);


    // REST FULL SERVICES FOR TABLE => pspagos
    $router->get('pspagos', ['uses' => 'PspagosController@showAllPspagos']);
    $router->post('pspagos', ['uses' => 'PspagosController@create']);
    $router->put(PSPAGOS_ROUTE, ['uses' => 'PspagosController@update']);
    $router->delete(PSPAGOS_ROUTE, ['uses' => 'PspagosController@delete']);

    // REST FULL SERVICES FOR TABLE => psfechaspago
    $router->get('psfechaspago/{id_prestamo}', ['uses' => 'PsfechaspagoController@showAllPsfechaspago']);
    $router->post('psfechaspago', ['uses' => 'PsfechaspagoController@create']);
    $router->put(PSFECHASPAGO_ROUTE, ['uses' => 'PsfechaspagoController@update']);
    $router->delete(PSFECHASPAGO_ROUTE, ['uses' => 'PsfechaspagoController@delete']);


    // REST FULL SERVICES FOR TABLE => psdocadjuntos


    $router->get('psdocadjuntos', ['uses' => 'PsdocadjuntosController@showAllPstdocadjuntos']);
    $router->get(PSDOCADJUNTOS_ROUTE, ['uses' => 'PsdocadjuntosController@showOnePsdocadjuntos']);
    $router->post('psdocadjuntos', ['uses' => 'PsdocadjuntosController@create']);
    $router->put(PSDOCADJUNTOS_ROUTE, ['uses' => 'PsdocadjuntosController@update']);
    $router->delete(PSDOCADJUNTOS_ROUTE, ['uses' => 'PsdocadjuntosController@delete']);



    // REST FULL SERVICES FOR TABLE => PspstiposistemaprestController
    $router->get('pstiposistemaprest', ['uses' => 'PspstiposistemaprestController@showAll']);
    $router->get(PSTIPOSISTEMAPREST_ROUTE, ['uses' => 'PspstiposistemaprestController@Show']);
    $router->post('pstiposistemaprest', ['uses' => 'PspstiposistemaprestController@create']);
    $router->put(PSTIPOSISTEMAPREST_ROUTE, ['uses' => 'PspstiposistemaprestController@update']);
    $router->delete(PSTIPOSISTEMAPREST_ROUTE, ['uses' => 'PspstiposistemaprestController@delete']);
    $router->get('listatiposistemaprest/', ['uses' => 'PspstiposistemaprestController@list']); // combo listas




    // REST FULL SERVICES FOR TABLE => psempresa

    $router->get(PSEMPRESA_ROUTE, ['uses' => 'PsempresaController@showOnePsempresa']);
    $router->put(PSEMPRESA_ROUTE, ['uses' => 'PsempresaController@update']);


    // COMPLEX QUERYS (selects of multiple tables, inner custom querys)

    $router->post('consultaTipoDocPlantilla', ['uses' => 'PsformapagoController@consultaTipoDocPlantilla']);
    $router->post('calcularCuotas', ['uses' => 'CuotasController@calcularCuotas']);
    $router->post('calcularCuotas2', ['uses' => 'CuotasController@calcularCuotas2']);
    $router->post('listadoPrestamos', ['uses' => 'PrestamosController@listadoPrestamos']);
    $router->post('prestamosCliente', ['uses' => 'PrestamosController@prestamosCliente']);
    $router->post('renderTemplates', ['uses' => 'PrestamosController@getPlantillasDocumentosPrestamo']);



    // COMPLEX PROCESS (procedures, multiples insert into table, bussiness logic etc.)


    $router->post('guardarPrestamo', ['uses' => 'PrestamosController@guardarPrestamo']);
    $router->get('generarVariablesPlantillas/{id_empresa}', ['uses' => 'PrestamosController@generarVariablesPlantillas']);
    $router->post('guardarArchivoAdjunto', ['uses' => 'GuardarArchivoController@guardarArchivoAdjunto']);
    $router->put('editarArchivoAdjunto', ['uses' => 'GuardarArchivoController@editarArchivoAdjunto']);
    $router->delete('eliminarPrestamo/{id_prestamo}', ['uses' => 'PrestamosController@eliminarPrestamo']);
    $router->get('capitalprestado/{id_empresa}', ['uses' => 'PrestamosController@totalcapital']);
    $router->post('totalprestadohoy', ['uses' => 'PrestamosController@totalprestadohoy']);
    $router->post('totalintereshoy', ['uses' => 'PrestamosController@totalintereshoy']);
    $router->post('totalinteres', ['uses' => 'PrestamosController@totalinteres']);
    $router->get('totalprestado/{id_empresa}', ['uses' => 'PrestamosController@totalprestado']);
    $router->post('totales_dashboard', ['uses' => 'PrestamosController@totales_dashboard']);
});
