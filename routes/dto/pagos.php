<?php

return [
    'GET /pspagos' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'POST /pspagos' => [
        'params' => [],
        'body' => 'object{id:int,id_cliente:int,id_user:int,id_empresa:int,id_prestamo:int,fecha_pago:string,fecha:string}',
        'response' => ['object{success:string}', 'object{error:string}'],
    ],
    'PUT /pspagos/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{valcuota:float}', 'response' => 'object'],
    'DELETE /pspagos/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],

    'GET /psfechaspago/{id_prestamo}' => ['params' => ['id_prestamo' => 'int'], 'body' => null, 'response' => 'array<object>'],
    'POST /psfechaspago' => ['params' => [], 'body' => 'object{id_prestamo:int,valor_cuota:float,valor_pagar:float,fecha_pago:string,ind_renovar:int,ind_estado:int,id_cliente:int,id_empresa:int}', 'response' => 'object'],
    'PUT /psfechaspago/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{ind_renovar:int}', 'response' => 'object'],
    'DELETE /psfechaspago/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],

    'GET /psdocadjuntos' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'GET /psdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'array<object>'],
    'POST /psdocadjuntos' => ['params' => [], 'body' => 'object{rutaadjunto:string,id_tdocadjunto:int,id_usu_cargarch:int,id_cliente:int,nombrearchivo:string,id_empresa:int}', 'response' => 'object'],
    'PUT /psdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nombrearchivo:string}', 'response' => 'object'],
    'DELETE /psdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
];
