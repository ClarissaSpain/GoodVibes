<?php

class DB{

    private $pdo;

    private function __construct($host, $dbname, $username, $password) {
        $pdo = new PDO('mysql:host='.$host.';dbName='.$dbname.';charset=utf8', $username, $password); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // return $pdo;
        $this->pdo = $pdo;
    }

    public function query($query, $params = array()) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        if (explode(' ', $query)[0]=='SELECT'){
        $data = $statement->fetchAll();
        return $data;
        }
}

}  