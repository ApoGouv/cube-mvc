<?php

namespace Core;

/**
 * View
 */
class View {
    public static function render($view, $args = []) {

        /**
         * Convert associative array into individual variables!
         * extract() : Import variables into the current symbol table from an array
         * EXTR_SKIP : If there is a collision, don't overwrite the existing variable.
         * Returns the number of variables successfully imported into the symbol table.
         */
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view"; // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            echo "{$file} not found.";
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args   Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = []){
        static $twig = null;

        if ($twig === null) {
            /* set the template directory, relative to the Core directory, using the following method:
             * the __DIR__ magic constant returns the directory of the current script (in this case Core),
             * and the dirname function returns the parent directory (in this case, the root of our framework).
            */
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }

}