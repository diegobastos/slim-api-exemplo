<?php

use Slim\App;

return function (App $app) {
    $routes = glob(__DIR__ . '/../routes/*.php');
    foreach ($routes as $routeFile) {
        require $routeFile;
    }
};