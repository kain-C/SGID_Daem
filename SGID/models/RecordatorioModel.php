<?php
class RecordatorioModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function guardarRecordatorio($usuario_id, $titulo, $descripcion, $fecha) {
        $stmt = $this->conexion->prepare("INSERT INTO recordatorio (usuario_id, titulo, descripcion, fecha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $titulo, $descripcion, $fecha);
        return $stmt->execute();
    }

    public function cargarRecordatorios() {
        $stmt = $this->conexion->prepare("
            SELECT r.*, u.NombreUsuario as usuario_nombre
            FROM recordatorio r
            JOIN usuarios u ON r.usuario_id = u.ID_Usuario
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function modificarRecordatorio($id, $titulo, $descripcion, $fecha) {
        $stmt = $this->conexion->prepare("UPDATE recordatorio SET titulo = ?, descripcion = ?, fecha = ? WHERE id = ?");
        $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $id);
        return $stmt->execute();
    }

    public function eliminarRecordatorio($id) {
        $stmt = $this->conexion->prepare("DELETE FROM recordatorio WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
