<?php
class MarcaModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    private function registrarHistorial($idUsuario, $accion) {
        $stmt = $this->conexion->prepare("INSERT INTO historial (ID_Usuario, FechaHora, Accion) VALUES (?, NOW(), ?)");
        $stmt->bind_param('is', $idUsuario, $accion);
        $stmt->execute();
    }

    public function agregarMarca($nombre, $idUsuario) {
        $stmt = $this->conexion->prepare("INSERT INTO marcas (nombre) VALUES (?)");
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param('s', $nombre);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error en la ejecución de la consulta: " . $stmt->error);
        } else {
            $this->registrarHistorial($idUsuario, "Se agregó la marca: $nombre");
        }
        return $success;
    }

    public function editarMarca($id, $nombre, $idUsuario) {
        $stmt = $this->conexion->prepare("UPDATE marcas SET nombre = ? WHERE id = ?");
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param('si', $nombre, $id);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error en la ejecución de la consulta: " . $stmt->error);
        } else {
            $this->registrarHistorial($idUsuario, "Se editó la marca con ID: $id a: $nombre");
        }
        return $success;
    }

    public function eliminarMarca($id, $idUsuario) {
        $stmt = $this->conexion->prepare("DELETE FROM marcas WHERE id = ?");
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error en la ejecución de la consulta: " . $stmt->error);
        } else {
            $this->registrarHistorial($idUsuario, "Se eliminó la marca con ID: $id");
        }
        return $success;
    }

    public function getMarcas() {
        $result = $this->conexion->query("SELECT id, nombre FROM marcas");
        if (!$result) {
            error_log("Error en la consulta de marcas: " . $this->conexion->error);
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
