<?php

namespace Core;

/**
 * Error and Exception handler
 */
class Error {

    /**
     * Error handler.
     * Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level    Error level
     * @param string $message   Error message
     * @param string $file  Filename the error was raised in
     * @param int $line Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line) {
        if (error_reporting() !== 0) { // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception) {

        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if($code != 404){
            $code = 500;
        }
        http_response_code($code); // Set status code for when sending response to the browser

        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" .get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            // setup the error log path and file
            date_default_timezone_set('Europe/Athens');
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            // setup the error message that will be printed in the log file
            $message = "Uncaught exception: '" .get_class($exception) . "'";
            $message .= " with message: '" . $exception->getMessage() . "'";
            $message .= "\n\tStack trace:" . $exception->getTraceAsString();
            $message .= "\n\tThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            //write the message to the log
            error_log($message);

            // Display generic message to user
            // echo "<h1>An error occurred</h1>";
            /*
            if ($code == 404) {
                echo "<h1>Oooops! Page not found.</h1>";
            } else {
                echo "<h1>An error occurred</h1>";
            }
            */
            View::renderTemplate("{$code}.html");
        }
    }

}