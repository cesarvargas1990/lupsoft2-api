<?php

$router->get('pspagos', $dto('PspagosController@showAllPspagos'));
$router->post('pspagos', $dto('PspagosController@create'));
$router->put(PSPAGOS_ROUTE, $dto('PspagosController@update'));
$router->delete(PSPAGOS_ROUTE, $dto('PspagosController@delete'));

$router->get('psfechaspago/{id_prestamo}', $dto('PsfechaspagoController@showAllPsfechaspago'));
$router->post('psfechaspago', $dto('PsfechaspagoController@create'));
$router->put(PSFECHASPAGO_ROUTE, $dto('PsfechaspagoController@update'));
$router->delete(PSFECHASPAGO_ROUTE, $dto('PsfechaspagoController@delete'));

$router->get('psdocadjuntos', $dto('PsdocadjuntosController@showAllPstdocadjuntos'));
$router->get(PSDOCADJUNTOS_ROUTE, $dto('PsdocadjuntosController@showOnePsdocadjuntos'));
$router->post('psdocadjuntos', $dto('PsdocadjuntosController@create'));
$router->put(PSDOCADJUNTOS_ROUTE, $dto('PsdocadjuntosController@update'));
$router->delete(PSDOCADJUNTOS_ROUTE, $dto('PsdocadjuntosController@delete'));
