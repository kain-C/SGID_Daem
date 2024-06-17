<?php

class DispositivoModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function agregarDispositivo($tipoDispositivo, $accion, $modelo, $numSerie, $descripcion, $marcaId, $establecimientoId, $estado) {
        $tipoDispositivo = mysqli_real_escape_string($this->conexion, $tipoDispositivo);
        $accion = mysqli_real_escape_string($this->conexion, $accion);
        $modelo = mysqli_real_escape_string($this->conexion, $modelo);
        $numSerie = mysqli_real_escape_string($this->conexion, $numSerie);
        $descripcion = mysqli_real_escape_string($this->conexion, $descripcion);
        $marcaId = mysqli_real_escape_string($this->conexion, $marcaId);
        $establecimientoId = mysqli_real_escape_string($this->conexion, $establecimientoId);
        $estado = mysqli_real_escape_string($this->conexion, $estado);

        $query = "INSERT INTO dispositivos (Tipo_dispositivo, accion, Modelo, Num_Serie, Descripcion, marca_id, ID_Establecimiento, Estado) 
                  VALUES ('$tipoDispositivo', '$accion', '$modelo', '$numSerie', '$descripcion', '$marcaId', '$establecimientoId', '$estado')";
        error_log("Ejecutando query: $query"); // Depuración
        mysqli_query($this->conexion, $query);
        if (mysqli_errno($this->conexion)) {
            error_log("Error en la consulta: " . mysqli_error($this->conexion)); // Depuración
            return false;
        }
        $idDispositivo = mysqli_insert_id($this->conexion);

        // Registrar en el historial
        if ($idDispositivo) {
            $nombreMarca = $this->obtenerNombreMarca($marcaId);
            $nombreEstablecimiento = $this->obtenerNombreEstablecimiento($establecimientoId);
            $accionHistorial = "Se ha registrado el dispositivo con ID: $idDispositivo, tipo: $tipoDispositivo, acción: $accion, modelo: $modelo, N°$numSerie, en el establecimiento: $nombreEstablecimiento, marca: $nombreMarca.";
            $this->registrarHistorial($_SESSION['ID_Usuario'], $accionHistorial);
        }

        return $idDispositivo;
    }

    public function registrarMantenimiento($idDispositivo, $descripcion, $idUsuario, $tipo) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $descripcion = mysqli_real_escape_string($this->conexion, $descripcion);
        $idUsuario = mysqli_real_escape_string($this->conexion, $idUsuario);
        $tipo = mysqli_real_escape_string($this->conexion, $tipo);
        date_default_timezone_set('America/Santiago');
        $fecha = date('Y-m-d H:i:s');
    
        $query = "INSERT INTO historial_mantenimiento (ID_Dispositivo, Fecha, Descripcion, ID_Usuario, tipo) 
                  VALUES ('$idDispositivo', '$fecha', '$descripcion', '$idUsuario', '$tipo')";
        return mysqli_query($this->conexion, $query);
    }

    public function obtenerHistorialMantenimiento($idDispositivo) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $query = "SELECT hm.*, u.NombreUsuario FROM historial_mantenimiento hm JOIN usuarios u ON hm.ID_Usuario = u.ID_Usuario WHERE hm.ID_Dispositivo = '$idDispositivo' ORDER BY hm.Fecha DESC";
        $result = mysqli_query($this->conexion, $query);
        $historial = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $historial[] = $row;
        }
        return $historial;
    }

    public function actualizarDispositivo($idDispositivo, $tipoDispositivo, $accion, $modelo, $numSerie, $descripcion, $marcaId, $establecimientoId, $estado) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $tipoDispositivo = mysqli_real_escape_string($this->conexion, $tipoDispositivo);
        $accion = mysqli_real_escape_string($this->conexion, $accion);
        $modelo = mysqli_real_escape_string($this->conexion, $modelo);
        $numSerie = mysqli_real_escape_string($this->conexion, $numSerie);
        $descripcion = mysqli_real_escape_string($this->conexion, $descripcion);
        $marcaId = mysqli_real_escape_string($this->conexion, $marcaId);
        $establecimientoId = mysqli_real_escape_string($this->conexion, $establecimientoId);
        $estado = mysqli_real_escape_string($this->conexion, $estado);

        // Obtener información del dispositivo antes de actualizar
        $dispositivoAnterior = $this->obtenerDispositivoPorId($idDispositivo);

        $query = "UPDATE dispositivos SET Tipo_dispositivo = '$tipoDispositivo', accion = '$accion', Modelo = '$modelo', Num_Serie = '$numSerie', 
                  Descripcion = '$descripcion', marca_id = '$marcaId', ID_Establecimiento = '$establecimientoId', Estado = '$estado'
                  WHERE ID_Dispositivo = '$idDispositivo'";
        error_log("Ejecutando query: $query"); // Depuración
        mysqli_query($this->conexion, $query);
        if (mysqli_errno($this->conexion)) {
            error_log("Error en la consulta: " . mysqli_error($this->conexion)); // Depuración
            return false;
        }

        // Registrar en el historial
        $cambios = [];
        if ($dispositivoAnterior['Tipo_dispositivo'] != $tipoDispositivo) {
            $cambios[] = "Tipo dispositivo: {$dispositivoAnterior['Tipo_dispositivo']} a $tipoDispositivo";
        }
        if ($dispositivoAnterior['accion'] != $accion) {
            $cambios[] = "Acción: {$dispositivoAnterior['accion']} a $accion";
        }
        if ($dispositivoAnterior['Modelo'] != $modelo) {
            $cambios[] = "Modelo: {$dispositivoAnterior['Modelo']} a $modelo";
        }
        if ($dispositivoAnterior['Num_Serie'] != $numSerie) {
            $cambios[] = "Número de Serie: {$dispositivoAnterior['Num_Serie']} a $numSerie";
        }
        if ($dispositivoAnterior['Descripcion'] != $descripcion) {
            $cambios[] = "Descripción actualizada";
        }
        if ($dispositivoAnterior['marca_id'] != $marcaId) {
            $nombreMarcaAnterior = $this->obtenerNombreMarca($dispositivoAnterior['marca_id']);
            $nombreMarcaNuevo = $this->obtenerNombreMarca($marcaId);
            $cambios[] = "Marca: $nombreMarcaAnterior a $nombreMarcaNuevo";
        }
        if ($dispositivoAnterior['ID_Establecimiento'] != $establecimientoId) {
            $nombreEstablecimientoAnterior = $this->obtenerNombreEstablecimiento($dispositivoAnterior['ID_Establecimiento']);
            $nombreEstablecimientoNuevo = $this->obtenerNombreEstablecimiento($establecimientoId);
            $cambios[] = "Establecimiento: $nombreEstablecimientoAnterior a $nombreEstablecimientoNuevo";
        }
        if ($dispositivoAnterior['Estado'] != $estado) {
            $cambios[] = "Estado: {$dispositivoAnterior['Estado']} a $estado";
        }

        if (!empty($cambios)) {
            $accionHistorial = "El dispositivo con ID: $idDispositivo ha sido actualizado. Cambios: " . implode(", ", $cambios) . ".";
            $this->registrarHistorial($_SESSION['ID_Usuario'], $accionHistorial);
        }

        return mysqli_affected_rows($this->conexion);
    }

    public function eliminarDispositivo($idDispositivo, $idUsuario) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);

        // Verificar si el ID_Usuario es válido
        if (!$this->esUsuarioValido($idUsuario)) {
            return false;
        }

        // Obtener información del dispositivo antes de eliminar
        $dispositivo = $this->obtenerDispositivoPorId($idDispositivo);
        $tipoDispositivo = $dispositivo['Tipo_dispositivo'];
        $accion = $dispositivo['accion'];
        $modelo = $dispositivo['Modelo'];
        $numSerie = $dispositivo['Num_Serie'];
        $establecimiento = $dispositivo['NombreEstablecimiento'];

        // Eliminar registros relacionados en no_reparado
        $queryEliminarNoReparado = "DELETE FROM no_reparado WHERE ID_Dispositivo = '$idDispositivo'";
        mysqli_query($this->conexion, $queryEliminarNoReparado);
        if (mysqli_errno($this->conexion)) {
            error_log("Error al eliminar registros en no_reparado: " . mysqli_error($this->conexion));
            return false;
        }

        // Eliminar el dispositivo
        $query = "DELETE FROM dispositivos WHERE ID_Dispositivo = '$idDispositivo'";
        error_log("Ejecutando query: $query"); // Depuración
        mysqli_query($this->conexion, $query);
        if (mysqli_errno($this->conexion)) {
            error_log("Error en la consulta dispositivos: " . mysqli_error($this->conexion)); // Depuración
            return false;
        }
        $filasAfectadas = mysqli_affected_rows($this->conexion);

        if ($filasAfectadas > 0) {
            // Registrar en el historial
            $accionHistorial = "El dispositivo con ID: $idDispositivo, tipo: $tipoDispositivo, acción: $accion, modelo: $modelo, N°$numSerie, del establecimiento $establecimiento ha sido eliminado.";
            $this->registrarHistorial($idUsuario, $accionHistorial);
        }

        return $filasAfectadas;
    }

    public function actualizarEstadoDispositivo($id, $estado, $descripcionReparacion = '', $descripcionProblema = '', $idUsuario) {
        $id = mysqli_real_escape_string($this->conexion, $id);
        $estado = mysqli_real_escape_string($this->conexion, $estado);
        $descripcionReparacion = mysqli_real_escape_string($this->conexion, $descripcionReparacion);
        $descripcionProblema = mysqli_real_escape_string($this->conexion, $descripcionProblema);

        // Verificar si el ID_Usuario es válido
        if (!$this->esUsuarioValido($idUsuario)) {
            return false;
        }

        // Obtener información del dispositivo para el historial
        $dispositivo = $this->obtenerDispositivoPorId($id);
        $tipoDispositivo = $dispositivo['Tipo_dispositivo'];
        $accion = $dispositivo['accion'];
        $modelo = $dispositivo['Modelo'];
        $numSerie = $dispositivo['Num_Serie'];
        $establecimiento = $dispositivo['NombreEstablecimiento'];

        $query = "UPDATE dispositivos SET Estado = ? WHERE ID_Dispositivo = ?";
        $stmt = $this->conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param('si', $estado, $id);

            if ($stmt->execute()) {
                if ($estado === 'No Reparado') {
                    $this->registrarNoReparado($id, $descripcionProblema, $idUsuario);
                }

                if ($estado === 'Reparado') {
                    $this->registrarReparacion($id, $descripcionReparacion, $idUsuario);
                }

                // Registrar en el historial
                $accionHistorial = ($estado === 'Reparado')
                    ? "El dispositivo con ID: $id, tipo: $tipoDispositivo, acción: $accion, modelo: $modelo, N°$numSerie, del establecimiento $establecimiento ha sido actualizado su estado a Reparado."
                    : "El dispositivo con ID: $id, tipo: $tipoDispositivo, acción: $accion, modelo: $modelo, N°$numSerie, del establecimiento $establecimiento ha tenido problemas que por el momento no han podido ser reparados.";
                $this->registrarHistorial($idUsuario, $accionHistorial);

                $stmt->close();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function registrarNoReparado($idDispositivo, $descripcionProblema, $idUsuario) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $descripcionProblema = mysqli_real_escape_string($this->conexion, $descripcionProblema);
        $idUsuario = mysqli_real_escape_string($this->conexion, $idUsuario);
        date_default_timezone_set('America/Santiago');
        $fecha = date('Y-m-d H:i:s');

        $query = "INSERT INTO no_reparado (ID_Dispositivo, Fecha, DescripcionErrores, ID_Usuario) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param('issi', $idDispositivo, $fecha, $descripcionProblema, $idUsuario);
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function registrarReparacion($idDispositivo, $descripcionReparacion, $idUsuario) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $descripcionReparacion = mysqli_real_escape_string($this->conexion, $descripcionReparacion);
        $idUsuario = mysqli_real_escape_string($this->conexion, $idUsuario);
        date_default_timezone_set('America/Santiago');
        $fecha = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO reparaciones (ID_Dispositivo, Fecha, Descripcion_Reparacion, ID_Usuario) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param('issi', $idDispositivo, $fecha, $descripcionReparacion, $idUsuario);
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function obtenerMarcas() {
        $query = "SELECT * FROM marcas";
        $result = mysqli_query($this->conexion, $query);
        $marcas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $marcas[] = ['id' => $row['id'], 'nombre' => $row['nombre']];
        }
        return $marcas;
    }

    public function obtenerEstablecimientos() {
        $query = "SELECT * FROM establecimientos";
        $result = mysqli_query($this->conexion, $query);
        $establecimientos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $establecimientos[] = ['ID_Establecimiento' => $row['ID_Establecimiento'], 'NombreEstablecimiento' => $row['NombreEstablecimiento']];
        }
        return $establecimientos;
    }

    public function obtenerDispositivos() {
        $query = "SELECT d.ID_Dispositivo, d.FechaRegistro, d.Tipo_dispositivo, d.accion, d.Modelo, d.Num_Serie, d.Descripcion, 
                         m.nombre AS NombreMarca, e.NombreEstablecimiento, d.Estado
                  FROM dispositivos d 
                  JOIN marcas m ON d.marca_id = m.id 
                  JOIN establecimientos e ON d.ID_Establecimiento = e.ID_Establecimiento";
        $result = mysqli_query($this->conexion, $query);
        $dispositivos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dispositivos[] = $row;
        }
        return $dispositivos;
    }

    public function obtenerDispositivoPorId($idDispositivo) {
        $idDispositivo = mysqli_real_escape_string($this->conexion, $idDispositivo);
        $query = "SELECT d.*, m.nombre AS NombreMarca, e.NombreEstablecimiento 
                  FROM dispositivos d 
                  JOIN marcas m ON d.marca_id = m.id 
                  JOIN establecimientos e ON d.ID_Establecimiento = e.ID_Establecimiento 
                  WHERE d.ID_Dispositivo = '$idDispositivo'";
        $result = mysqli_query($this->conexion, $query);
        return mysqli_fetch_assoc($result);
    }

    public function validarNumeroSerie($numSerie, $id = null) {
        $numSerie = mysqli_real_escape_string($this->conexion, $numSerie);

        $query = "SELECT COUNT(*) as count FROM dispositivos WHERE Num_Serie = ?";
        if ($id) {
            $query .= " AND ID_Dispositivo != ?";
        }

        $stmt = $this->conexion->prepare($query);
        if ($id) {
            $stmt->bind_param('si', $numSerie, $id);
        } else {
            $stmt->bind_param('s', $numSerie);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    public function registrarHistorial($idUsuario, $accion) {
        date_default_timezone_set('America/Santiago');
        $idUsuario = mysqli_real_escape_string($this->conexion, $idUsuario);
        $accion = mysqli_real_escape_string($this->conexion, $accion);
        $fechaHora = date('Y-m-d H:i:s');

        $query = "INSERT INTO historial (ID_Usuario, FechaHora, Accion) 
                  VALUES ('$idUsuario', '$fechaHora', '$accion')";
        return mysqli_query($this->conexion, $query);
    }

    private function esUsuarioValido($idUsuario) {
        $idUsuario = mysqli_real_escape_string($this->conexion, $idUsuario);
        $query = "SELECT COUNT(*) as count FROM usuarios WHERE ID_Usuario = '$idUsuario'";
        $result = mysqli_query($this->conexion, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] > 0;
    }

    private function obtenerNombreMarca($marcaId) {
        $query = "SELECT nombre FROM marcas WHERE id = '$marcaId'";
        $result = mysqli_query($this->conexion, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['nombre'];
    }

    private function obtenerNombreEstablecimiento($establecimientoId) {
        $query = "SELECT NombreEstablecimiento FROM establecimientos WHERE ID_Establecimiento = '$establecimientoId'";
        $result = mysqli_query($this->conexion, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['NombreEstablecimiento'];
    }
    public function obtenerDispositivosNoReparados() {
        $query = "SELECT d.ID_Dispositivo, d.FechaRegistro, d.Tipo_dispositivo, d.accion, d.Modelo, d.Num_Serie, d.Descripcion, 
                         m.nombre AS NombreMarca, e.NombreEstablecimiento, d.Estado
                  FROM dispositivos d 
                  JOIN marcas m ON d.marca_id = m.id 
                  JOIN establecimientos e ON d.ID_Establecimiento = e.ID_Establecimiento
                  WHERE d.Estado = 'No Reparado'";
        $result = mysqli_query($this->conexion, $query);
        $dispositivos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dispositivos[] = $row;
        }
        return $dispositivos;
    }
    public function obtenerDispositivosPorSerie($numSerie) {
        $numSerie = mysqli_real_escape_string($this->conexion, $numSerie);
        $query = "SELECT ID_Dispositivo, FechaRegistro, Descripcion, Estado 
                  FROM dispositivos 
                  WHERE Num_Serie = '$numSerie' AND (Estado = 'Reparado' OR Estado = 'No Reparado')
                  ORDER BY FechaRegistro DESC";
        error_log("Query: $query"); // Añadido para depuración
        $result = mysqli_query($this->conexion, $query);
        if (mysqli_errno($this->conexion)) {
            error_log("Error en la consulta: " . mysqli_error($this->conexion)); // Depuración
            return [];
        }
        $dispositivos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dispositivos[] = $row;
        }
        error_log("Dispositivos: " . print_r($dispositivos, true)); // Añadido para depuración
        return $dispositivos;
    }
    public function validarNumeroSerieEnEspera($numSerie) {
        $numSerie = mysqli_real_escape_string($this->conexion, $numSerie);
        $query = "SELECT COUNT(*) as count FROM dispositivos WHERE Num_Serie = '$numSerie' AND Estado = 'En espera'";
        $result = mysqli_query($this->conexion, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] > 0;
    }

    
}
?>
