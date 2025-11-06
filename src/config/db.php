<?php
$host ='localhost';
$dbname ='prueba_tecnica';
$username='root';
$password ='';

$conexion = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = NEW PDO($conexion,$username,$password);

} catch(PDOException $e){
    echo 'Error de conexion'.$e->getMessage();

}