<?php

$routes->group('api', ['namespace' => 'NathanReus\CI4APIFramework\Controllers', 'filter' => 'throttle'], function($routes) {
    $routes->post('auth/login', 'AuthController::attemptLogin', ['as' => 'api-login']);
    $routes->post('auth/refresh', 'AuthController::refreshToken', ['as' => 'api-refresh']);
    $routes->post('auth/logout', 'AuthController::logout', ['as' => 'api-logout']);
});