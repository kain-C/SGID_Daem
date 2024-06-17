<?php

class Establecimiento {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM establecimientos";
        $result = mysqli_query($this->conexion, $sql);
        $data = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function eliminar($id) {
        $sql = "DELETE FROM establecimientos WHERE ID_Establecimiento = $id";
        return mysqli_query($this->conexion, $sql);
    }

    public function editar($id, $nombre, $direccion, $localidad, $rbd, $tipo, $otro_tipo = null) {
        $otro_tipo_sql = ($tipo == 'Otro' && $otro_tipo) ? ", otro_tipo='$otro_tipo'" : '';
        $sql = "UPDATE establecimientos SET 
                    NombreEstablecimiento='$nombre', 
                    Direccion='$direccion', 
                    Localidad='$localidad', 
                    RBD='$rbd', 
                    tipo_establecimiento='$tipo' 
                    $otro_tipo_sql
                WHERE ID_Establecimiento=$id";
        return mysqli_query($this->conexion, $sql);
    }

    public function crear($nombre, $direccion, $localidad, $rbd, $tipo, $otro_tipo = null) {
        $otro_tipo_sql = ($tipo == 'Otro' && $otro_tipo) ? ", otro_tipo" : '';
        $otro_tipo_value = ($tipo == 'Otro' && $otro_tipo) ? ", '$otro_tipo'" : '';
        $sql = "INSERT INTO establecimientos 
                    (NombreEstablecimiento, Direccion, Localidad, RBD, tipo_establecimiento $otro_tipo_sql) 
                VALUES 
                    ('$nombre', '$direccion', '$localidad', '$rbd', '$tipo' $otro_tipo_value)";
        return mysqli_query($this->conexion, $sql);
    }

    public function existeDuplicado($nombre, $direccion, $localidad, $rbd, $tipo, $id = null) {
        $sql = "SELECT * FROM establecimientos 
                WHERE NombreEstablecimiento = '$nombre' 
                AND Direccion = '$direccion' 
                AND Localidad = '$localidad' 
                AND RBD = '$rbd' 
                AND tipo_establecimiento = '$tipo'";
        if ($id) {
            $sql .= " AND ID_Establecimiento != $id";
        }
        $result = mysqli_query($this->conexion, $sql);
        return mysqli_num_rows($result) > 0;
    }
}

?>
