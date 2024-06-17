<?php
// models/ReparacionesModel.php
require_once '../config/conexion.php';

class ReparacionesModel {
    private $conexion;

    public function __construct() {
        global $conexion; // Utilizamos la conexión global existente
        $this->conexion = $conexion;
    }

    public function getReparaciones() {
        $sql = "SELECT reparaciones.ID_Reparacion, reparaciones.ID_Dispositivo, reparaciones.Fecha, reparaciones.Descripcion_Reparacion, 
            dispositivos.Modelo AS NombreDispositivo, dispositivos.Num_Serie, dispositivos.Descripcion AS DescripcionDispositivo, 
            dispositivos.accion, dispositivos.Tipo_dispositivo, dispositivos.Modelo,
            establecimientos.NombreEstablecimiento, establecimientos.Localidad, establecimientos.RBD, establecimientos.tipo_establecimiento, 
            marcas.Nombre AS NombreMarca 
            FROM reparaciones 
            INNER JOIN dispositivos ON reparaciones.ID_Dispositivo = dispositivos.ID_Dispositivo
            INNER JOIN establecimientos ON dispositivos.ID_Establecimiento = establecimientos.ID_Establecimiento
            INNER JOIN marcas ON dispositivos.marca_id = marcas.id";

        $result = $this->conexion->query($sql);

        if ($result === false) {
            die(json_encode(array("error" => "Error en la consulta SQL: " . $this->conexion->error)));
        }

        $data = array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function updateDescripcionReparacion($idReparacion, $descripcionReparacion) {
        $sql = "UPDATE reparaciones SET Descripcion_Reparacion = ? WHERE ID_Reparacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $descripcionReparacion, $idReparacion);
        
        return $stmt->execute();
    }
}

?>

