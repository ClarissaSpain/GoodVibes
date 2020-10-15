<?php

class DB{
    private static function connect() {
        $pdo = new PDO('mysql:host=localhost;dbName=GoodVibes;charset=utf8','root', 'markie11'); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;

            // $dbServername = "localhost";
            // $dbUsername = "root";
            // $dbPassword = "montana";
            // $dbName = "goodvibes";

            // $conn = new PDO($dbServername, $dbUsername, $dbPassword, $dbName);
    }

    public static function query($query, $params = array()) {
        $statement = PDO::connect()->prepare($query);
        $statement->execute($params);
        // $data = $statement->fetchAll();
        // return $data;
}

}