<?php
require_once('../config/conexion.php');
require_once('../models/DispositivoModel.php');

$modelo = new DispositivoModel($conexion);

$action = $_GET['action'] ?? '';

session_start();
$idUsuario = $_SESSION['ID_Usuario'] ?? 0;

function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

switch ($action) {
    case 'getDispositivos':
        try {
            $dispositivos = $modelo->obtenerDispositivos();
            sendJsonResponse(['data' => $dispositivos]);
        } catch (Exception $e) {
            sendJsonResponse(['data' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'getMarcas':
        try {
            $marcas = $modelo->obtenerMarcas();
            sendJsonResponse(['data' => $marcas]);
        } catch (Exception $e) {
            sendJsonResponse(['data' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'getEstablecimientos':
        try {
            $establecimientos = $modelo->obtenerEstablecimientos();
            sendJsonResponse(['data' => $establecimientos]);
        } catch (Exception $e) {
            sendJsonResponse(['data' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'validarNumeroSerie':
        try {
            $numSerie = $_POST['num_serie'] ?? '';
            $id = $_POST['id'] ?? null;
            $duplicado = $modelo->validarNumeroSerie($numSerie, $id);
            sendJsonResponse(['duplicado' => $duplicado]);
        } catch (Exception $e) {
            sendJsonResponse(['duplicado' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'checkNumSerie':
        $dispositivoController->checkNumSerie();
        break;

    case 'agregarDispositivo':
        try {
            $tipoDispositivo = $_POST['tipo_dispositivo'];
            $accion = $_POST['accion'];
            $modeloDispositivo = $_POST['modelo'];
            $numSerie = $_POST['num_serie'];
            $descripcion = $_POST['descripcion'];
            $marcaId = $_POST['marcaId'];
            $establecimientoId = $_POST['establecimientoId'];
            $estado = 'En espera';

            $idDispositivo = $modelo->agregarDispositivo($tipoDispositivo, $accion, $modeloDispositivo, $numSerie, $descripcion, $marcaId, $establecimientoId, $estado);
            if ($idDispositivo) {
                sendJsonResponse(['success' => true, 'id' => $idDispositivo]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al agregar el dispositivo.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'editarDispositivo':
        try {
            $id = $_POST['id'];
            $tipoDispositivo = $_POST['tipo_dispositivo'];
            $accion = $_POST['accion'];
            $modeloDispositivo = $_POST['modelo'];
            $numSerie = $_POST['num_serie'];
            $descripcion = $_POST['descripcion'];
            $marcaId = $_POST['marcaId'];
            $establecimientoId = $_POST['establecimientoId'];
            $estado = 'En espera';

            $success = $modelo->actualizarDispositivo($id, $tipoDispositivo, $accion, $modeloDispositivo, $numSerie, $descripcion, $marcaId, $establecimientoId, $estado);

            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al actualizar el dispositivo.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'eliminarDispositivo':
        try {
            $id = $_POST['id'];
            $success = $modelo->eliminarDispositivo($id, $idUsuario);

            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al eliminar el dispositivo.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'repararDispositivo':
        try {
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            $descripcionReparacion = $_POST['descripcion_reparacion'] ?? '';
            $descripcionProblema = $_POST['descripcion_problema'] ?? '';

            $success = $modelo->actualizarEstadoDispositivo($id, $estado, $descripcionReparacion, $descripcionProblema, $idUsuario);

            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al actualizar el estado del dispositivo.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'getDispositivo':
        try {
            $id = $_GET['id'];
            $dispositivo = $modelo->obtenerDispositivoPorId($id);
            if ($dispositivo) {
                sendJsonResponse(['success' => true, 'data' => $dispositivo]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'No se encontró el dispositivo.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'registrarMantenimiento':
        try {
            $idDispositivo = $_POST['id_dispositivo'];
            $descripcion = $_POST['descripcion'];
            $tipo = $_POST['tipo'];
            $success = $modelo->registrarMantenimiento($idDispositivo, $descripcion, $idUsuario, $tipo);

            if ($success) {
                sendJsonResponse(['success' => true]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Error al registrar el mantenimiento.']);
            }
        } catch (Exception $e) {
            sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'getDispositivosNoReparados':
        try {
            $dispositivosNoReparados = $modelo->obtenerDispositivosNoReparados();
            sendJsonResponse(['data' => $dispositivosNoReparados]);
        } catch (Exception $e) {
            sendJsonResponse(['data' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'obtenerDispositivosPorSerie':
        try {
            $numSerie = $_GET['num_serie'];
            error_log("Num Serie: $numSerie"); // Añadido para depuración
            $dispositivos = $modelo->obtenerDispositivosPorSerie($numSerie);
            error_log("Dispositivos en controlador: " . print_r($dispositivos, true)); // Añadido para depuración
            sendJsonResponse(['data' => $dispositivos]);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage()); // Añadido para depuración
            sendJsonResponse(['data' => [], 'error' => $e->getMessage()]);
        }
        break;
        case 'checkNumSerie':
            try {
                $numSerie = $_POST['num_serie'];
                $exists = $modelo->validarNumeroSerieEnEspera($numSerie);
                sendJsonResponse(['exists' => $exists]);
            } catch (Exception $e) {
                sendJsonResponse(['error' => $e->getMessage()]);
            }
            break;
        
    default:
        sendJsonResponse(['error' => 'Acción no válida']);
        break;
}
?>
