<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('a',function(){
    dd(\App\Models\User::create([
        'full_name' => 'Pouya Parsaei'
    ]));
});

$router->group(['prefix' => 'api/v1'],function() use ($router){
    $router->group(['prefix' => 'users'],function() use ($router) {
        $router->post('','Api\V1\UserController@store');
        $router->put('','Api\V1\UserController@updateInfo');
        $router->put('change-password','Api\V1\UserController@updatePassword');
        $router->get('','Api\V1\UserController@index');
        $router->delete('','Api\V1\UserController@delete');
        $router->get('test','Api\V1\UserController@test');
    });

    $router->group(['prefix' => 'categories'],function() use ($router) {
        $router->get('','Api\V1\CategoryController@index');
        $router->post('','Api\V1\CategoryController@store');
        $router->delete('','Api\V1\CategoryController@delete');
        $router->put('','Api\V1\CategoryController@update');
       });
});
