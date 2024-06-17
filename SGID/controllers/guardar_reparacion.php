<?php
session_start();
require_once('../config/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDispositivo = $_POST['idDispositivo'];
    $descripcionReparacion = $_POST['descripcionReparacion'];

    // Insertar la reparación en la tabla de reparaciones
    $query = "INSERT INTO reparaciones (ID_Dispositivo, Descripcion_Reparacion) VALUES ('$idDispositivo', '$descripcionReparacion')";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo 'Reparación guardada correctamente.';
    } else {
        echo 'Error al guardar la reparación.';
    }
} else {
    echo 'Acceso no autorizado.';
}
?>
