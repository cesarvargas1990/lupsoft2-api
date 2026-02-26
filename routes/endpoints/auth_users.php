<?php

$router->post('auth/register', $dto('AuthController@register'));
$router->post('auth/login', $dto('AuthController@login'));
$router->post('auth/logout', $dto('AuthController@logout'));
$router->get('profile', $dto('UserController@profile'));
$router->get('users/{id}', $dto('UserController@singleUser'));
$router->get('users', $dto('UserController@allUsers'));
$router->get('cobradores/{id}', $dto('UserController@getUsers'));
