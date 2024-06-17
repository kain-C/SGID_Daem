<?php
class HistorialModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerHistorial() {
        $sql = "SELECT h.ID_Historial, u.NombreUsuario AS ID_Usuario, h.FechaHora, h.Accion
                FROM historial h
                INNER JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario";
        return $this->ejecutarConsulta($sql);
    }

    public function obtenerHistorialHoy() {
        $hoy = date('Y-m-d');
        $sql = "SELECT h.ID_Historial, u.NombreUsuario AS ID_Usuario, h.FechaHora, h.Accion
                FROM historial h
                INNER JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario
                WHERE DATE(h.FechaHora) = '$hoy'";
        return $this->ejecutarConsulta($sql);
    }

    public function obtenerHistorialAyer() {
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT h.ID_Historial, u.NombreUsuario AS ID_Usuario, h.FechaHora, h.Accion
                FROM historial h
                INNER JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario
                WHERE DATE(h.FechaHora) = '$ayer'";
        return $this->ejecutarConsulta($sql);
    }

    public function obtenerHistorialMes() {
        $mesInicio = date('Y-m-01');
        $sql = "SELECT h.ID_Historial, u.NombreUsuario AS ID_Usuario, h.FechaHora, h.Accion
                FROM historial h
                INNER JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario
                WHERE h.FechaHora >= '$mesInicio'";
        return $this->ejecutarConsulta($sql);
    }

    public function obtenerHistorialAnio() {
        $anioInicio = date('Y-01-01');
        $sql = "SELECT h.ID_Historial, u.NombreUsuario AS ID_Usuario, h.FechaHora, h.Accion
                FROM historial h
                INNER JOIN usuarios u ON h.ID_Usuario = u.ID_Usuario
                WHERE h.FechaHora >= '$anioInicio'";
        return $this->ejecutarConsulta($sql);
    }

    private function ejecutarConsulta($sql) {
        $resultado = mysqli_query($this->conexion, $sql);
        if (!$resultado) {
            die("Error al consultar la base de datos: " . mysqli_error($this->conexion));
        }

        $historial = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $historial[] = $fila;
        }

        mysqli_free_result($resultado);
        return $historial;
    }
}
?>
