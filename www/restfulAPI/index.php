<?php

require_once("db_connection.php");

$db = new DB("127.0.0.1", "goodvibes", "root", "markie11");

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    echo json_encode(($db->query("SELECT * FROM goodvibes.users")));

} else if($_SERVER['REQUEST_METHOD'] == "POST") {
    echo "post";
} else {
    http_response_code(405);
}

?>