<?php
require_once '../config/conexion.php';
require_once '../models/HistorialModel.php';

class HistorialController {
    private $model;

    public function __construct($conexion) {
        $this->model = new HistorialModel($conexion);
    }

    public function obtenerHistorial() {
        return $this->model->obtenerHistorial();
    }

    public function obtenerHistorialHoy() {
        return $this->model->obtenerHistorialHoy();
    }

    public function obtenerHistorialAyer() {
        return $this->model->obtenerHistorialAyer();
    }

    public function obtenerHistorialMes() {
        return $this->model->obtenerHistorialMes();
    }

    public function obtenerHistorialAnio() {
        return $this->model->obtenerHistorialAnio();
    }
}

$controller = new HistorialController($conexion);

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    switch ($accion) {
        case 'hoy':
            $historial = $controller->obtenerHistorialHoy();
            break;
        case 'ayer':
            $historial = $controller->obtenerHistorialAyer();
            break;
        case 'mes':
            $historial = $controller->obtenerHistorialMes();
            break;
        case 'año':
            $historial = $controller->obtenerHistorialAnio();
            break;
        case 'todos':
        default:
            $historial = $controller->obtenerHistorial();
            break;
    }
    header('Content-Type: application/json');
    echo json_encode(['data' => $historial]);
}
?>
