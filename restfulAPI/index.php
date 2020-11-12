<?php

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    echo "Hello World";

} else if($_SERVER['REQUEST_METHOD'] == "POST") {
    echo "post";
}

?>