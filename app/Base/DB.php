<?php
namespace Base;

class DB {

    static function connect() {
        $db = new \PDO(
                'mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME,DB_USER, DB_PASS
        );
        return $db;
    }

}
