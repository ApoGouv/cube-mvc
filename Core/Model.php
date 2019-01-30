<?php

namespace Core;

use PDO;

/**
 * Base model
 */
abstract class Model {

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB(){
        static $db = null;

        if($db === null) {
            $host = 'localhost';
            $dbname = 'cube_mvc_fwk_db';
            $username = 'root';
            $password = 'mysql';

            try{
                $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$username, $password);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }
}