<?php

$router->get('pstipodocidenti', $dto('PstipodocidentiController@ShowPstipodocidenti'));
$router->post('pstipodocidenti', $dto('PstipodocidentiController@create'));
$router->put('pstipodocidenti/{id}', $dto('PstipodocidentiController@update'));
$router->delete('pstipodocidenti/{id}', $dto('PstipodocidentiController@delete'));

$router->get('listaformaspago/{id_empresa}', $dto('PsformapagoController@ShowPsformapago'));

$router->get('psperiodopago', $dto('PsperiodopagoController@showAllpsperiodospago'));
$router->get(PSPERIODO_PAGO_ROUTE, $dto('PsperiodopagoController@showOnePsperiodopago'));
$router->post('psperiodopago', $dto('PsperiodopagoController@create'));
$router->put(PSPERIODO_PAGO_ROUTE, $dto('PsperiodopagoController@update'));
$router->delete(PSPERIODO_PAGO_ROUTE, $dto('PsperiodopagoController@delete'));
$router->get('listaperiodopago', $dto('PsperiodopagoController@ShowPsperiodopago'));

$router->get('pstdocadjuntos', $dto('PstdocadjuntosController@showAllPstdocadjuntos'));
$router->get(PSTDOCADJUNTOS_ID, $dto('PstdocadjuntosController@showOnePstdocadjuntos'));
$router->post('pstdocadjuntos', $dto('PstdocadjuntosController@create'));
$router->put(PSTDOCADJUNTOS_ID, $dto('PstdocadjuntosController@update'));
$router->delete(PSTDOCADJUNTOS_ID, $dto('PstdocadjuntosController@delete'));
$router->get('listatdocadjuntos/{id_empresa}', $dto('PstdocadjuntosController@ShowPstdocadjuntos'));

$router->get('pstdocplant', $dto('PstdocplantController@showAllpstdocplant'));
$router->get(PSTDOCPLANT_ID, $dto('PstdocplantController@ShowPstdocplant'));
$router->post('pstdocplant', $dto('PstdocplantController@create'));
$router->put(PSTDOCPLANT_ID, $dto('PstdocplantController@update'));
$router->delete(PSTDOCPLANT_ID, $dto('PstdocplantController@delete'));

$router->get('pstiposistemaprest', $dto('PspstiposistemaprestController@showAll'));
$router->get(PSTIPOSISTEMAPREST_ROUTE, $dto('PspstiposistemaprestController@Show'));
$router->post('pstiposistemaprest', $dto('PspstiposistemaprestController@create'));
$router->put(PSTIPOSISTEMAPREST_ROUTE, $dto('PspstiposistemaprestController@update'));
$router->delete(PSTIPOSISTEMAPREST_ROUTE, $dto('PspstiposistemaprestController@delete'));
$router->get('listatiposistemaprest/', $dto('PspstiposistemaprestController@list'));

$router->get(PSEMPRESA_ROUTE, $dto('PsempresaController@showOnePsempresa'));
$router->put(PSEMPRESA_ROUTE, $dto('PsempresaController@update'));
