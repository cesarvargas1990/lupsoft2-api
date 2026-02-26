<?php

$router->post('psclientes/{id_empresa}', $dto('PsclientesController@showAllPsclientes'));
$router->get(PSCLIENTES_ROUTE, $dto('PsclientesController@showOnePsclientes'));
$router->post('psclientes', $dto('PsclientesController@create'));
$router->put(PSCLIENTES_ROUTE, $dto('PsclientesController@update'));
$router->delete(PSCLIENTES_ROUTE, $dto('PsclientesController@delete'));
$router->get('listadoclientes/{id}', $dto('PsclientesController@ShowPsclientes'));
