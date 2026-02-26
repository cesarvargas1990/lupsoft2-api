<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Http\Request;

define('PSCLIENTES_ROUTE', 'psclientes/{id}');
define('PSDOCADJUNTOS_ROUTE', 'psdocadjuntos/{id}');
define('PSTIPOSISTEMAPREST_ROUTE', 'pstiposistemaprest/{id}');
define('PSEMPRESA_ROUTE', 'psempresa/{id}');
define('PSPERIODO_PAGO_ROUTE', 'psperiodopago/{id}');
define('PSPAGOS_ROUTE', 'pspagos/{id}');
define('PSFECHASPAGO_ROUTE', 'psfechaspago/{id}');
define('PSTDOCPLANT_ID', 'pstdocplant/{id}');
define('PSTDOCADJUNTOS_ID', 'pstdocadjuntos/{id}');

$ROUTE_DTO_MAP = array_merge(
    require __DIR__ . '/dto/base.php',
    require __DIR__ . '/dto/clientes.php',
    require __DIR__ . '/dto/catalogos.php',
    require __DIR__ . '/dto/pagos.php',
    require __DIR__ . '/dto/prestamos.php'
);

app()->instance('route.dto.map', $ROUTE_DTO_MAP);

$dto = function ($uses) {
    return ['middleware' => 'dto.validate', 'uses' => $uses];
};

$dtoClosure = function ($handler) {
    return ['middleware' => 'dto.validate', $handler];
};

require __DIR__ . '/endpoints/public.php';

$router->group(['prefix' => ''], function () use ($router, $dto) {
    require __DIR__ . '/endpoints/auth_users.php';
    require __DIR__ . '/endpoints/clientes.php';
    require __DIR__ . '/endpoints/catalogos.php';
    require __DIR__ . '/endpoints/pagos.php';
    require __DIR__ . '/endpoints/prestamos.php';
});
