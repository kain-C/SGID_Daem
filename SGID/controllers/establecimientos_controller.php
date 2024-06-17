<?php

header("Content-Type: application/json");
require '../config/conexion.php';
require '../models/EstablecimientosModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$establecimiento = new Establecimiento($conexion);

switch ($method) {
    case 'GET':
        // Leer todos los establecimientos
        $data = $establecimiento->obtenerTodos();
        echo json_encode($data);
        break;

    case 'POST':
        $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

        if ($accion == 'eliminar') {
            // Eliminar un establecimiento
            $id = $_POST['id'];
            if ($establecimiento->eliminar($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
            }
        } elseif ($accion == 'editar') {
            // Editar un establecimiento
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $localidad = $_POST['localidad'];
            $rbd = $_POST['rbd'];
            $tipo = $_POST['tipo'];
            $otro_tipo = isset($_POST['otro_tipo']) ? $_POST['otro_tipo'] : null;

            if ($establecimiento->existeDuplicado($nombre, $direccion, $localidad, $rbd, $tipo, $id)) {
                echo json_encode(['success' => false, 'error' => 'Establecimiento duplicado']);
            } elseif ($establecimiento->editar($id, $nombre, $direccion, $localidad, $rbd, $tipo, $otro_tipo)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
            }
        } else {
            // Crear un nuevo establecimiento
            $nombre = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $localidad = $_POST['localidad'];
            $rbd = $_POST['rbd'];
            $tipo = $_POST['tipo'];
            $otro_tipo = isset($_POST['otro_tipo']) ? $_POST['otro_tipo'] : null;

            if ($establecimiento->existeDuplicado($nombre, $direccion, $localidad, $rbd, $tipo)) {
                echo json_encode(['success' => false, 'error' => 'Establecimiento duplicado']);
            } elseif ($establecimiento->crear($nombre, $direccion, $localidad, $rbd, $tipo, $otro_tipo)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
            }
        }
        break;

    default:
        echo json_encode(['error' => 'Método no soportado']);
        break;
}

?>
