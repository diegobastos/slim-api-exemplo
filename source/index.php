<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
require_once __DIR__ . "/database.php";

$app = AppFactory::create();

/** 
 * Carregar middlewares
 */ 
(require __DIR__ . '/middlewares.php')($app);

/**
 * Carregar todas as rotas
 */
$routes = require __DIR__ . '/routes.php';
$routes($app);

/**
 * Executar a aplicação
 */
$app->run();
