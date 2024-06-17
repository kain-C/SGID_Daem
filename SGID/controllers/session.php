<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Asignar variables de sesión
$nombreUsuario = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$idUsuario = isset($_SESSION['ID_Usuario']) ? $_SESSION['ID_Usuario'] : '';
$correoUsuario = isset($_SESSION['CorreoElectronico']) ? $_SESSION['CorreoElectronico'] : '';
$tipoUsuario = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : '';

?>
