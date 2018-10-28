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

$router -> group(['prefix' => 'positions'], function() use ($router) {
    $router -> get('/', 'PositionController@index');    
});

$router -> group(['prefix' => 'users'], function() use ($router) {
    $router -> get('/', 'UserController@index');    
    $router -> get('/self', 'UserController@showSelf');
    $router -> get('/{id}', 'UserController@show');
    $router -> delete('/{id}', 'UserController@destroy');
    $router -> put('/profile', 'UserController@updateProfile');
    $router -> put('/password', 'UserController@updatePassword');
    $router -> put('/position/{id}', 'UserController@updatePosition');
});

$router -> group(['prefix' => 'logs'], function() use ($router) {
    $router -> get('/', 'LogController@index');
    // $router -> get('/{id}', 'LogController@show');
    // $router -> put('/{id}', 'LogController@updateProfile');
    // $router -> put('/{id}/password', 'LogController@updatePassword');
    // $router -> put('/{id}/position', 'LogController@updatePosition');
    // $router -> delete('/{id}', 'LogController@destroy');
});

$router -> group(['prefix' => 'categories'], function() use ($router) {
    $router -> get('/', 'CategoryController@index');    
    $router -> get('/{id}', 'CategoryController@show');
    $router -> post('/', 'CategoryController@store');
    $router -> put('/{id}', 'CategoryController@update');
    $router -> delete('/{id}', 'CategoryController@destroy');
});

$router -> group(['prefix' => 'subcategories'], function() use ($router) {
    $router -> get('/', 'SubCategoryController@index');    
    $router -> get('/{id}', 'SubCategoryController@show');
    $router -> post('/', 'SubCategoryController@store');
    $router -> put('/{id}', 'SubCategoryController@update');
    $router -> delete('/{id}', 'SubCategoryController@destroy');
});

$router -> group(['prefix' => 'units'], function() use ($router) {
    $router -> get('/', 'UnitController@index');    
    $router -> get('/{id}', 'UnitController@show');
    $router -> post('/', 'UnitController@store');
    $router -> put('/{id}', 'UnitController@update');
    $router -> delete('/{id}', 'UnitController@destroy');
});

$router -> group(['prefix' => 'brands'], function() use ($router) {
    $router -> get('/', 'BrandController@index');    
    $router -> get('/{id}', 'BrandController@show');
    $router -> post('/', 'BrandController@store');
    $router -> put('/{id}', 'BrandController@update');
    $router -> delete('/{id}', 'BrandController@destroy');
});
