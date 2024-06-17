<?php
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$base_de_datos = 'SGID';

$conexion = mysqli_connect($host, $usuario, $contraseña, $base_de_datos);

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>
