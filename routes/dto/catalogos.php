<?php

return [
    'GET /pstipodocidenti' => ['params' => [], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],
    'POST /pstipodocidenti' => ['params' => [], 'body' => 'object{codtipdocid:int,nomtipodocumento:string}', 'response' => 'object'],
    'PUT /pstipodocidenti/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nomtipodocumento:string}', 'response' => 'object'],
    'DELETE /pstipodocidenti/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
    'GET /listaformaspago/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],

    'GET /psperiodopago' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'GET /psperiodopago/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object'],
    'POST /psperiodopago' => ['params' => [], 'body' => 'object{nomperiodopago:string,id_empresa:int}', 'response' => 'object'],
    'PUT /psperiodopago/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nomperiodopago:string}', 'response' => 'object'],
    'DELETE /psperiodopago/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
    'GET /listaperiodopago' => ['params' => [], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],

    'GET /pstdocadjuntos' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'GET /pstdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object'],
    'POST /pstdocadjuntos' => ['params' => [], 'body' => 'object{nombre:string,id_empresa:int}', 'response' => 'object'],
    'PUT /pstdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nombre:string}', 'response' => 'object'],
    'DELETE /pstdocadjuntos/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
    'GET /listatdocadjuntos/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],

    'GET /pstdocplant' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'GET /pstdocplant/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => ['object', 'array<object{value:int,label:string}>']],
    'POST /pstdocplant' => ['params' => [], 'body' => 'object{nombre:string,plantilla_html:string,id_empresa:int}', 'response' => 'object'],
    'PUT /pstdocplant/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nombre:string}', 'response' => 'object'],
    'DELETE /pstdocplant/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],

    'GET /pstiposistemaprest' => ['params' => [], 'body' => null, 'response' => 'array<object>'],
    'GET /pstiposistemaprest/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => ['object', 'array<object{value:int,label:string}>']],
    'POST /pstiposistemaprest' => ['params' => [], 'body' => 'object{codtipsistemap:int,nomtipsistemap:string,formula:string}', 'response' => 'object'],
    'PUT /pstiposistemaprest/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nomtipsistemap:string}', 'response' => 'object'],
    'DELETE /pstiposistemaprest/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
    'GET /listatiposistemaprest/' => ['params' => [], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],

    'GET /psempresa/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object'],
    'PUT /psempresa/{id}' => ['params' => ['id' => 'int'], 'body' => 'object{nombre:string,nit:string,ddirec:string,ciudad:string,telefono:string,pagina:string,email:string,vlr_capinicial:float,firma:string}', 'response' => 'object'],
];
