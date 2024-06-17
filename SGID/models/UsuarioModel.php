<?php
require_once('../config/conexion.php');

class UsuarioModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function verificarCredenciales($email, $password) {
        $email = mysqli_real_escape_string($this->conexion, $email);

        // Consulta para obtener el usuario por correo electrónico
        $sql = "SELECT ID_Usuario, NombreUsuario, CorreoElectronico, pass, tipo FROM usuarios WHERE CorreoElectronico = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['pass'];

            //var_dump($hashedPassword);
            //die();

            //Mostrar la contraseña ingresada y la hash de la base de datos
            error_log("Password ingresada: " . $password);
            error_log("Password hash de la base de datos: " . $hashedPassword);

            // Verificar si la contraseña ingresada coincide con la almacenada (hash o texto plano)
            if (password_verify($password, $hashedPassword)) {
                error_log("Verificación de contraseña hash exitosa");
                return [
                    'success' => true,
                    'username' => $row['NombreUsuario'],
                    'idUsuario' => $row['ID_Usuario'],
                    'correoUsuario' => $row['CorreoElectronico'],
                    'tipo' => $row['tipo']
                ];
            } elseif ($password === $hashedPassword) {
                error_log("Verificación de contraseña en texto plano exitosa");
                return [
                    'success' => true,
                    'username' => $row['NombreUsuario'],
                    'idUsuario' => $row['ID_Usuario'],
                    'correoUsuario' => $row['CorreoElectronico'],
                    'tipo' => $row['tipo'] // Añadir tipo de usuario
                ];
            } else {
                error_log("Error: Contraseña incorrecta");
                return ['success' => false, 'message' => "Correo electrónico o contraseña incorrectos."];
            }
        } else {
            error_log("Error: Correo electrónico no encontrado");
            return ['success' => false, 'message' => "Correo electrónico o contraseña incorrectos."];
        }
    }

    public function registrarHistorial($idUsuario, $accion) {
        $accion = mysqli_real_escape_string($this->conexion, $accion);
        date_default_timezone_set('America/Santiago');
        $fechaHora = date('Y-m-d H:i:s');

        // Registro en la tabla historial
        $sql = "INSERT INTO historial (ID_Usuario, FechaHora, Accion) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("iss", $idUsuario, $fechaHora, $accion);
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Error al registrar en historial: ' . $stmt->error];
        }
        return ['success' => true];
    }
}
?>


