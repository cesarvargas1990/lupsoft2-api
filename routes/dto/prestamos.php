<?php

return [
    'POST /consultaTipoDocPlantilla' => ['params' => [], 'body' => 'object{id_empresa:int}', 'response' => 'array<object>'],
    'POST /calcularCuotas' => ['params' => [], 'body' => 'object{id_periodo_pago:int,id_sistema_pago?:int|string,numcuotas:int,porcint:float,valorpres:float}', 'response' => 'array<object>'],
    'POST /calcularCuotas2' => ['params' => [], 'body' => 'object{id_periodo_pago:int,id_sistema_pago?:int|string,numcuotas:int,porcint:float,valorpres:float}', 'response' => 'object'],
    'POST /listadoPrestamos' => ['params' => [], 'body' => 'object{id_empresa:int}', 'response' => 'array<object>'],
    'POST /prestamosCliente' => ['params' => [], 'body' => 'object{id_empresa:int,id_cliente:int}', 'response' => 'array<object>'],
    'POST /renderTemplates' => ['params' => [], 'body' => 'object{id_empresa:int,id_prestamo:int}', 'response' => 'array<object>'],

    'POST /guardarPrestamo' => ['params' => [], 'body' => 'object{id_empresa:int,id_cliente:int,valorpres:float,numcuotas:int,porcint:float,id_periodo_pago:int,id_sistema_pago:int,fec_inicial:string,id_cobrador:int,id_usureg:int,fecha:string}', 'response' => ['int', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'GET /generarVariablesPlantillas/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => 'array<object{title:string,content:string}>'],
    'POST /guardarArchivoAdjunto' => ['params' => [], 'body' => 'object{id_tdocadjunto:int,id_empresa:int,id_cliente:int,id_usuario:int,filename:string,image:string}', 'response' => 'object{status:string,data?:string,error?:string}'],
    'PUT /editarArchivoAdjunto' => ['params' => [], 'body' => 'object{id_cliente:int,id_usuario:int,id_empresa:int,filename:string,id_tdocadjunto:array<int>,image:array<string>}', 'response' => 'null'],

    'DELETE /eliminarPrestamo/{id_prestamo}' => ['params' => ['id_prestamo' => 'int'], 'body' => null, 'response' => 'null'],
    'GET /capitalprestado/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => ['string', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'POST /totalprestadohoy' => ['params' => [], 'body' => 'object{id_empresa:int,fecha:string}', 'response' => ['string', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'POST /totalintereshoy' => ['params' => [], 'body' => 'object{id_empresa:int,fecha:string}', 'response' => ['string', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'POST /totalinteres' => ['params' => [], 'body' => 'object{id_empresa:int}', 'response' => ['string', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'GET /totalprestado/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => ['string', 'object{message:string,errorCode:int,lineError:int,file:string}']],
    'POST /totales_dashboard' => ['params' => [], 'body' => 'object{id_empresa:int,fecha:string}', 'response' => 'object{total_capital_prestado:string,total_interes:string,total_interes_hoy:string,total_prestado_hoy:string,total_prestado:string,ahora:string}'],
];
