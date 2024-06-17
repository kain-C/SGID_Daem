<?php
// controllers/ReparacionesController.php
require_once '../models/ReparacionesModel.php';

class ReparacionesController {
    private $model;

    public function __construct() {
        $this->model = new ReparacionesModel();
    }

    public function getReparaciones() {
        $data = $this->model->getReparaciones();
        $response = array("data" => $data);

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function updateDescripcionReparacion() {
        $idReparacion = $_POST['idReparacion'];
        $descripcionReparacion = $_POST['descripcionReparacion'];

        $result = $this->model->updateDescripcionReparacion($idReparacion, $descripcionReparacion);

        if ($result) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error" => "Error al actualizar la descripción de reparación."));
        }
    }
}

// Manejo de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ReparacionesController();
    $controller->getReparaciones();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'updateDescripcionReparacion') {
    $controller = new ReparacionesController();
    $controller->updateDescripcionReparacion();
}

?>
