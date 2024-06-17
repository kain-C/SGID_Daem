<?php
include '../models/dispGrafics.php';

header('Content-Type: application/json');

$data = [];
$data['estadoDispositivos'] = contarDispositivosPorEstado();
$data['ingresosDelMes'] = obtenerIngresosDelMes();

echo json_encode($data);
?>
