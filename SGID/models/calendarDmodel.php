<?php
class DispositivoModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerDispositivosEnEspera() {
        $query = "SELECT ID_Dispositivo, Modelo, FechaRegistro, accion, Tipo_dispositivo FROM dispositivos WHERE Estado = 'En Espera'";
        $result = mysqli_query($this->conexion, $query);

        if (!$result) {
            die("Error al ejecutar la consulta: " . mysqli_error($this->conexion));
        }

        $devices = array();
        while($row = mysqli_fetch_assoc($result)) {
            $devices[] = $row;
        }

        return $devices;
    }
}
?>

