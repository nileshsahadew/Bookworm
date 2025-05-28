<?php

$server_name = "localhost";
$user_name = "root";
$password = "";
$db_name = "bookworm";

try
{
 $conn = new PDO("mysql:host=$server_name;dbname=$db_name", $user_name, $password);
}
catch(PDOException $e)
 {
    echo   "<br>" . $e->getMessage();
 }



?>