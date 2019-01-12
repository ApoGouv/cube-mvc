<?php

/**
 * Front controller
 */

//  echo 'Requested URL = "' . $_SERVER['QUERY_STRING'] . '"';

/**
 * Routing
 */
require '../Core/Router.php';

$router = new Router();

// echo get_class($router);

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('posts', ['controller' => 'Posts', 'action' => 'index']);
//$router->add('posts/new', ['controller' => 'Posts', 'action' => 'new']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}');

// Display the routing table
// echo '<pre>' . var_export($router->getRoutes(), true) . '</pre>';
highlight_string("\n<?php\n\$router->getRoutes() =\n" . var_export($router->getRoutes(), true) . ";\n?>");

// Match the requested route
$url = $_SERVER['QUERY_STRING'];

if ($router->match($url)){
    // echo '<pre>' . var_export($router->getParams(), true) . '</pre>';
    /*highlight_string("\n<?php\n\$url =\n" . var_export($url, true) . ";\n?>");*/
    highlight_string("\n<?php\n\$router->getParams() =\n" . var_export($router->getParams(), true) . ";\n?>");
} else {
    echo "No route found for URL '$url'";
}
