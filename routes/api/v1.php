<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'api/v1'],function() use ($router){
    $router->group(['prefix' => 'users'],function() use ($router) {
        $router->post('','Api\V1\UserController@store');

    });
});
