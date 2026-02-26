<?php

return [
    'GET /upload/documentosAdjuntos/{filepath:.*}' => [
        'params' => ['filepath' => 'string'],
        'body' => null,
        'response' => ['binary', 'object{error:string}'],
    ],
    'GET /' => ['params' => [], 'body' => null, 'response' => 'string'],
    'POST /auth/register' => [
        'params' => [],
        'body' => 'object{name:string,email:string,password:string,password_confirmation:string}',
        'response' => 'object{user:object,message:string}',
    ],
    'POST /auth/login' => [
        'params' => [],
        'body' => 'object{email:string,password:string}',
        'response' => 'object{id:int,name:string,email:string,access_token:string,token_type:string,status:string,menu_usuario:array<mixed>,permisos:array<mixed>,expires_in:int}',
    ],
    'POST /auth/logout' => ['params' => [], 'body' => null, 'response' => 'object{message:string}'],
    'GET /profile' => ['params' => [], 'body' => null, 'response' => 'object{user:object}'],
    'GET /users/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'object{user:object}'],
    'GET /users' => ['params' => [], 'body' => null, 'response' => 'object{users:array<object>}'],
    'GET /cobradores/{id}' => ['params' => ['id' => 'int'], 'body' => null, 'response' => 'array<object{value:int,label:string}>'],
];
