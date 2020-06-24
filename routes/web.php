<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return response()->json([]);
});

$router->group(['prefix' => 'projects'], function ($router) {
    $router->get('/', 'ProjectController@index');
    $router->get('/{project}/', 'ProjectController@get');
    $router->get('/{project}/status/', 'ProjectController@status');
    $router->post('/{project}/deploy/', 'ProjectController@deploy');
    $router->post('/{project}/rollback/', 'ProjectController@rollback');
});
