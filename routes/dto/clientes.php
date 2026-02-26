<?php

return [
    'POST /psclientes/{id_empresa}' => ['params' => ['id_empresa' => 'int'], 'body' => null, 'response' => 'array<object>'],
    'GET /psclientes/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => ['object', 'object{message:string}']],
    'POST /psclientes' => [
        'params' => [],
        'body' => 'object{nomcliente:string,id_tipo_docid:int,numdocumento:string,id_empresa:int,id_cobrador:int,id_user:int,ciudad?:string,telefijo?:string,celular?:string,direcasa?:string,diretrabajo?:string,ubicasa?:string,ubictrabajo?:string,ref1?:string,ref2?:string,email?:string,fch_expdocumento?:string,fch_nacimiento?:string}',
        'response' => 'object',
    ],
    'PUT /psclientes/{id}' => ['params' => ['id' => 'int'], 'body' => 'object', 'response' => 'object'],
    'DELETE /psclientes/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{message:string}'],
    'GET /listadoclientes/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],
];
