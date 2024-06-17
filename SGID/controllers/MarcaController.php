<?php
require_once('../config/conexion.php');
require_once('../models/MarcaModel.php');

$modelo = new MarcaModel($conexion);

$action = $_GET['action'] ?? '';

session_start();
$idUsuario = $_SESSION['ID_Usuario'] ?? 0;

function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

switch ($action) {
    case 'agregarMarca':
        $nombre = $_POST['nombre'] ?? '';
        if (empty($nombre)) {
            sendJsonResponse(['success' => false, 'message' => 'El nombre no puede estar vacío.']);
        }
        $success = $modelo->agregarMarca($nombre, $idUsuario);
        if ($success) {
            sendJsonResponse(['success' => true]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error al agregar la marca.']);
        }
        break;
        case 'editarMarca':
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            if (empty($id) || empty($nombre)) {
                sendJsonResponse(['success' => false, 'message' => 'El ID y el nombre no pueden estar vacíos.']);
            }
            $success = $modelo->editarMarca($id, $nombre, $idUsuario);
            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al editar la marca.']);
            }
            break;
    case 'eliminarMarca':
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            sendJsonResponse(['success' => false, 'message' => 'El ID no puede estar vacío.']);
        }
        // Verificar si hay dispositivos asociados a esta marca antes de eliminar
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM dispositivos WHERE marca_id = ?");
        if (!$stmt) {
            sendJsonResponse(['success' => false, 'message' => 'Error al preparar la consulta.']);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // No se puede eliminar la marca porque hay dispositivos asociados
            sendJsonResponse(['success' => false, 'message' => 'No se puede eliminar la marca porque tiene dispositivos asociados.']);
        } else {
            $success = $modelo->eliminarMarca($id, $idUsuario);
            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                // Registrar el mensaje de error
                error_log("Error al eliminar la marca con ID: $id");
                sendJsonResponse(['success' => false, 'message' => 'Error al eliminar la marca.']);
            }
        }
        break;
    case 'getMarcas':
        $marcas = $modelo->getMarcas();
        sendJsonResponse(['data' => $marcas]);
        break;
    default:
        sendJsonResponse(['error' => 'Acción no válida']);
        break;
}
?>
