<?php

/**
 * Front controller
 */

// Require the Posts controller class
//require '../App/Controllers/Posts.php';

// Require the Router class
//require '../Core/Router.php';

/**
 * Autoloader
 */
spl_autoload_register(function ($class) {
    $root = dirname(__DIR__); //get the parent directory
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)){ // check if file exists and is readable or not
        require $file;
    }
});

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);


// // Display the routing table
// // echo '<pre>' . var_export($router->getRoutes(), true) . '</pre>';
/*highlight_string("\n<?php\n\$router->getRoutes() =\n" . var_export($router->getRoutes(), true) . ";\n?>");*/

// // Match the requested route
// $url = $_SERVER['QUERY_STRING'];

// if ($router->match($url)){
//     // echo '<pre>' . var_export($router->getParams(), true) . '</pre>';
     /*highlight_string("\n<?php\n\$url =\n" . var_export($url, true) . ";\n?>");*/
     /*highlight_string("\n<?php\n\$router->getParams() =\n" . var_export($router->getParams(), true) . ";\n?>");*/
// } else {
//     echo "No route found for URL '$url'";
// }

$router->dispatch($_SERVER['QUERY_STRING']);


