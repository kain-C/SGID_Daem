<?php
session_start();
require_once('../config/conexion.php');
require_once('../models/UsuarioModel.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginController = new LoginController($conexion);
    $resultado = $loginController->iniciarSesion($_POST['email'], $_POST['password']);
    
    if ($resultado['success']) {
        echo "Inicio de sesión exitoso. Redireccionando...";
    } else {
        echo "Error: " . $resultado['message'];
    }
}

class LoginController {
    private $usuarioModel;

    public function __construct($conexion) {
        $this->usuarioModel = new UsuarioModel($conexion);
    }

    public function iniciarSesion($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => "Correo electrónico y contraseña son requeridos."];
        }

        $resultado = $this->usuarioModel->verificarCredenciales($email, $password);
    
        if ($resultado['success']) {
            $_SESSION['username'] = $resultado['username'];
            $_SESSION['ID_Usuario'] = $resultado['idUsuario'];
            $_SESSION['CorreoElectronico'] = $resultado['correoUsuario'];
            $_SESSION['tipo'] = $resultado['tipo']; // Asigna el tipo de usuario

            $idUsuario = $resultado['idUsuario'];
            $accion = "Inicio de Sesión";
            $this->usuarioModel->registrarHistorial($idUsuario, $accion);
    
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => "Correo electrónico o contraseña incorrectos."];
        }
    }
}
?>





