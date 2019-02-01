<?php

namespace Core;

/**
 * Router
 */
class Router {

    /**
     * Associative array of routes (the routing table)
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    /**
     * Add a route to the routing table
     *
     * @param string $route     The route URL
     * @param array  $params    Parameters (controller, action, etc.)
     * @return void
     */
    public function add($route, $params = []){

        // Convert the route to a regular expression:
        // escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variable with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Get all the routes from the routing table
     *
     * @return array
     */
    public function getRoutes(){
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params
     * property if a route is found.
     *
     * @param string $url   The route URL
     * @return boolean  true if a match found, false otherwise
     */
    public function match($url){

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)){
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * Dispatch the route, creating the controller object and running the
     * action method
     *
     * @param $string $url The route URL
     * @return void
     */
    public function dispatch($url){

        $url = $this->removeQueryStringVariables($url);

        // Match the URL to the routing table
        if ($this->match($url)) {

            // Get the controller parameter
            $controller = $this->params['controller'];

            // Convert the controller parameter name to SturdlyCaps naming format
            $controller = $this->convertToSturdlyCaps($controller);
            // get the namespace
            $controller = $this->getNamespace() . $controller;

            // check is a class exist with name = $controller
            if (class_exists($controller)) {
                // make a new object of that controller, passing the params array
                $controller_object = new $controller($this->params);

                // Get the action parameter
                $action = $this->params['action'];

                // Convert the action parameter name to camelCase naming format
                $action = $this->convertToCamelCase($action);

                // check that the name of the action doesn't end in "Action"
                // (or "action" - the "i" flag means it's case insensitive).
                // If it doesn't, then the method is called. If it does, then an exception is raised.
                if (preg_match('/action$/i', $action) == 0) {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method {$action} (in controller {$controller}) not found.");
                }
            } else {
                //echo "Controller class $controller not found. ";
                throw new \Exception("Controller class {$controller} not found.");
            }
        }else {
            //echo 'No route matched. ';
            throw new \Exception('No route matched.', 404);
        }
    }

    /**
     * Convert the string with hyphens to SturlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToSturdlyCaps($string){
        // ucwords — Uppercase the first character of each word in a string
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToCamelCase($string){
        // lcfirst - Returns a string with the first character of str lowercased
        //           if that character is alphabetic.
        return lcfirst($this->convertToSturdlyCaps($string));
    }

    /**
     * Remove the query string variables from the URL (if any). AS the full
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     *
     * URL                                          $_SERVER['QUERY_STRING']    Route
     * ---------------------------------------------------------------------------------------
     * localhost/cube_mvc_fwk                       ''                          ''
     * localhost/cube_mvc_fwk/?                     ''                          ''
     * localhost/cube_mvc_fwk/?page=1               page=1                      ''
     * localhost/cube_mvc_fwk/posts?page=1          posts&page=1                posts
     * localhost/cube_mvc_fwk/posts/index           posts/index                 posts/index
     * localhost/cube_mvc_fwk/posts/index?page=1    posts/index&page=1          posts/index
     *
     * A URL of the format localhost/cube_mvc_fwk/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable)
     *
     * @param string $url The full URL
     * @return string The URL with the query string variables removed
     */
    protected function removeQueryStringVariables($url){
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    /**
     * Get the namespace for the controller class. The namespace defined in the
     * route parameters is added if present
     *
     * @return string   The namespace - defaults to App\Controllers\
     */
    protected function getNamespace(){
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}