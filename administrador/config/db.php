<?php
$host="localhost";
$db="sitio";
$usuario="root";
$contra="";

try {
    $conexion=new PDO("mysql:host=$host; dbname=$db", $usuario, $contra);
    
} catch (Exception $e) {
echo $e->getMessage();
}


?>