<?php
include '../config/conexion.php';

function contarDispositivosPorEstado() {
    global $conexion;

    $query = "SELECT Estado, COUNT(*) as count FROM dispositivos GROUP BY Estado";
    $result = mysqli_query($conexion, $query);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $data = [
        'enEspera' => 0,
        'reparados' => 0,
        'noReparados' => 0,
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        switch (strtolower(trim($row['Estado']))) {
            case 'en espera':
                $data['enEspera'] = $row['count'];
                break;
            case 'reparado':
                $data['reparados'] = $row['count'];
                break;
            case 'no reparado':
                $data['noReparados'] = $row['count'];
                break;
        }
    }

    return $data;
}

function obtenerIngresosDelMes() {
    global $conexion;

    $data = [];

    $query = "SELECT DATE(FechaRegistro) as fecha, COUNT(*) as cantidad 
              FROM dispositivos 
              WHERE MONTH(FechaRegistro) = MONTH(CURDATE()) 
              AND YEAR(FechaRegistro) = YEAR(CURDATE()) 
              GROUP BY DATE(FechaRegistro)";
    $result = mysqli_query($conexion, $query);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['fecha']] = $row['cantidad'];
    }

    return $data;
}
?>
