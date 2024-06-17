<?php
require_once '../config/conexion.php';
require_once '../models/RecordatorioModel.php';

class RecordatorioController {
    private $model;

    public function __construct($conexion) {
        $this->model = new RecordatorioModel($conexion);
    }

    public function guardarRecordatorio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : null;
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';

            if ($this->model->guardarRecordatorio($usuario_id, $titulo, $descripcion, $fecha)) {
                echo "Recordatorio guardado exitosamente";
            } else {
                echo "Error al guardar el recordatorio";
            }
        }
    }

    public function cargarRecordatorios() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $recordatorios = $this->model->cargarRecordatorios();
            echo json_encode($recordatorios);
        }
    }

    public function modificarRecordatorio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';

            if ($id === null) {
                echo "Error: id no proporcionado.";
                return;
            }

            if ($this->model->modificarRecordatorio($id, $titulo, $descripcion, $fecha)) {
                echo "Recordatorio modificado exitosamente";
            } else {
                echo "Error al modificar el recordatorio";
            }
        }
    }

    public function eliminarRecordatorio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = isset($_POST['id']) ? $_POST['id'] : null;

            if ($id === null) {
                echo "Error: id no proporcionado.";
                return;
            }

            if ($this->model->eliminarRecordatorio($id)) {
                echo "Recordatorio eliminado exitosamente";
            } else {
                echo "Error al eliminar el recordatorio";
            }
        }
    }
}

// Crear instancia del controlador con la conexión existente
$controller = new RecordatorioController($conexion);

// Manejar las solicitudes
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'guardar':
            $controller->guardarRecordatorio();
            break;
        case 'cargar':
            $controller->cargarRecordatorios();
            break;
        case 'modificar':
            $controller->modificarRecordatorio();
            break;
        case 'eliminar':
            $controller->eliminarRecordatorio();
            break;
    }
}
?>
