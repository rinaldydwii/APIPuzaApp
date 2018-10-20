<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    $res['success'] = true;
    $res['result'] = "Welcome to API Puza App";
    return response($res);
});

$router -> post('/login', 'AuthController@login');
$router -> post('/register', 'AuthController@register');
// $router -> get('/user/{id}', ['middleware' => 'auth', 'uses' => 'UserController@get_user']); 
$router -> group(['prefix' => 'categories'], function() use ($router) {
    $router -> get('/', 'CategoryController@index');    
    $router -> get('/{id}', 'CategoryController@show');
    $router -> post('/', 'CategoryController@store');
    $router -> put('/{id}', 'CategoryController@update');
    $router -> delete('/{id}', 'CategoryController@destroy');
});