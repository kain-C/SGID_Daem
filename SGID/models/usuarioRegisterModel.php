<?php
require_once('../config/conexion.php');

class UsuarioModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getUsuarios($start, $length, $search) {
        $query = "SELECT * FROM usuarios WHERE NombreUsuario LIKE ? LIMIT ?, ?";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $searchTerm = "%$search%";
        $stmt->bind_param("sii", $searchTerm, $start, $length);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUsuariosCount() {
        $query = "SELECT COUNT(*) as count FROM usuarios";
        $result = $this->conexion->query($query);
        if (!$result) {
            throw new Exception("Error al obtener el conteo de usuarios: " . $this->conexion->error);
        }
        return $result->fetch_assoc()['count'];
    }

    public function agregarUsuario($nombre, $email, $password, $tipo) {
        if ($this->usuarioExiste($nombre, $email)) {
            throw new Exception("El usuario o correo ya existe");
        }

        // Hashear la contraseña antes de almacenarla
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        error_log("Contraseña hasheada al agregar: " . $passwordHash);

        $query = "INSERT INTO usuarios (NombreUsuario, CorreoElectronico, pass, tipo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("ssss", $nombre, $email, $passwordHash, $tipo);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result;
    }

    public function editarUsuario($idUsuario, $nombre, $email, $password, $tipo) {
        $passwordHash = null;
        if (!empty($password)) {
            // Hashear la nueva contraseña si se proporciona
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            error_log("Contraseña hasheada al editar: " . $passwordHash);
        }

        $query = "UPDATE usuarios SET NombreUsuario = ?, CorreoElectronico = ?, tipo = ?";
        if ($passwordHash) {
            $query .= ", pass = ?";
        }
        $query .= " WHERE ID_Usuario = ?";

        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        if ($passwordHash) {
            $stmt->bind_param("ssssi", $nombre, $email, $tipo, $passwordHash, $idUsuario);
        } else {
            $stmt->bind_param("sssi", $nombre, $email, $tipo, $idUsuario);
        }
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result;
    }

    public function eliminarUsuario($idUsuario) {
        $query = "DELETE FROM usuarios WHERE ID_Usuario = ?";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("i", $idUsuario);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result;
    }

    public function getUsuario($idUsuario) {
        $query = "SELECT * FROM usuarios WHERE ID_Usuario = ?";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }

    private function usuarioExiste($nombre, $email) {
        $query = "SELECT COUNT(*) as count FROM usuarios WHERE NombreUsuario = ? OR CorreoElectronico = ?";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("ss", $nombre, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return $result->fetch_assoc()['count'] > 0;
    }

    public function verificarCredenciales($email, $password) {
        $query = "SELECT ID_Usuario, NombreUsuario, CorreoElectronico, pass, tipo FROM usuarios WHERE CorreoElectronico = ?";
        $stmt = $this->conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Depuración: Mostrar la contraseña ingresada y la hash de la base de datos
            error_log("Password ingresada: " . $password);
            error_log("Password hash de la base de datos: " . $row['pass']);
            if (password_verify($password, $row['pass'])) {
                error_log("Verificación de contraseña hash exitosa");
                return [
                    'success' => true,
                    'username' => $row['NombreUsuario'],
                    'idUsuario' => $row['ID_Usuario'],
                    'correoUsuario' => $row['CorreoElectronico'],
                    'tipo' => $row['tipo']
                ];
            } elseif ($password === $row['pass']) {
                error_log("Verificación de contraseña en texto plano exitosa");
                return [
                    'success' => true,
                    'username' => $row['NombreUsuario'],
                    'idUsuario' => $row['ID_Usuario'],
                    'correoUsuario' => $row['CorreoElectronico'],
                    'tipo' => $row['tipo']
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

        $sql = "INSERT INTO historial (ID_Usuario, FechaHora, Accion) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
        $stmt->bind_param("iss", $idUsuario, $fechaHora, $accion);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        return ['success' => true];
    }
}
?>
