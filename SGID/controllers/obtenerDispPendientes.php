<?php
include '..config/conexion.php'; // Incluir el archivo de conexión

// Consulta para obtener dispositivos pendientes
$sql = "SELECT ID_Dispositivo, Modelo, FechaRegistro FROM dispositivos WHERE Estado = 'pendiente'";
$result = mysqli_query($conexion, $sql);

$devices = array();
if (mysqli_num_rows($result) > 0) {
    // Salida de datos de cada fila
    while ($row = mysqli_fetch_assoc($result)) {
        $devices[] = $row;
    }
} else {
    echo json_encode([]); // Devolver un array vacío si no hay resultados
}

// Cerrar conexión
mysqli_close($conexion);

// Devolver los datos en formato JSON
echo json_encode($devices);
?>
