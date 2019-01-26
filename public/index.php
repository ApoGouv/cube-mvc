<?php

/**
 * Front controller
 */


/**
 * Composer Autoloader - will autoload all installed packages classes
 */
require '../vendor/autoload.php';

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

// Match the requested route
$router->dispatch($_SERVER['QUERY_STRING']);
