<?php

/** @var \Buki\Router\Router $router */

$router->get('/', 'HomeController@index');

$router->get('/test', function () {
    return 'Test Route';
});
