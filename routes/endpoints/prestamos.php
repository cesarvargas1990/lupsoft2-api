<?php

$router->post('consultaTipoDocPlantilla', $dto('PsformapagoController@consultaTipoDocPlantilla'));
$router->post('calcularCuotas', $dto('CuotasController@calcularCuotas'));
$router->post('calcularCuotas2', $dto('CuotasController@calcularCuotas2'));
$router->post('listadoPrestamos', $dto('PrestamosController@listadoPrestamos'));
$router->post('prestamosCliente', $dto('PrestamosController@prestamosCliente'));
$router->post('renderTemplates', $dto('PrestamosController@getPlantillasDocumentosPrestamo'));

$router->post('guardarPrestamo', $dto('PrestamosController@guardarPrestamo'));
$router->get('generarVariablesPlantillas/{id_empresa}', $dto('PrestamosController@generarVariablesPlantillas'));
$router->post('guardarArchivoAdjunto', $dto('GuardarArchivoController@guardarArchivoAdjunto'));
$router->put('editarArchivoAdjunto', $dto('GuardarArchivoController@editarArchivoAdjunto'));
$router->delete('eliminarPrestamo/{id_prestamo}', $dto('PrestamosController@eliminarPrestamo'));
$router->get('capitalprestado/{id_empresa}', $dto('PrestamosController@totalcapital'));
$router->post('totalprestadohoy', $dto('PrestamosController@totalprestadohoy'));
$router->post('totalintereshoy', $dto('PrestamosController@totalintereshoy'));
$router->post('totalinteres', $dto('PrestamosController@totalinteres'));
$router->get('totalprestado/{id_empresa}', $dto('PrestamosController@totalprestado'));
$router->post('totales_dashboard', $dto('PrestamosController@totales_dashboard'));
