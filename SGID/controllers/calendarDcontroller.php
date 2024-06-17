<?php

include '../config/conexion.php';  
include '../models/calendarDmodel.php';  

header('Content-Type: application/json');

$dispositivoModel = new DispositivoModel($conexion);
$devices = $dispositivoModel->obtenerDispositivosEnEspera();

echo json_encode($devices);

mysqli_close($conexion); 
?>
