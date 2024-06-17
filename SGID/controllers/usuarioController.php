<?php
require_once '../config/conexion.php'; 
require_once '../models/usuarioRegisterModel.php';

$model = new UsuarioModel($conexion);

$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'getUsuarios':
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $search = $_POST['search']['value'] ?? '';

        try {
            $usuarios = $model->getUsuarios($start, $length, $search);
            $total = $model->getUsuariosCount();

            echo json_encode([
                "draw" => intval($_POST['draw']),
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => $usuarios
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "error" => true,
                "message" => "Error al obtener usuarios: " . $e->getMessage()
            ]);
        }
        break;

    case 'agregarUsuario':
        $nombre = trim($_POST['NombreUsuario']);
        $email = trim($_POST['CorreoElectronico']);
        $password = trim($_POST['pass']);
        $confirmPassword = trim($_POST['confirm_pass']);
        $tipo = $_POST['tipo'];

        if (!in_array($tipo, ['administrador', 'usuarioRegular'])) {
            echo json_encode([
                "success" => false,
                "message" => "Tipo de usuario no válido"
            ]);
            break;
        }

        if ($password !== $confirmPassword) {
            echo json_encode([
                "success" => false,
                "message" => "Las contraseñas no coinciden"
            ]);
            break;
        }

        try {
            $result = $model->agregarUsuario($nombre, $email, $password, $tipo);

            if ($result) {
                echo json_encode(["success" => true, "message" => "Usuario registrado correctamente"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al agregar usuario"]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        break;

    case 'editarUsuario':
        $idUsuario = $_POST['ID_Usuario'];
        $nombre = trim($_POST['NombreUsuario']);
        $email = trim($_POST['CorreoElectronico']);
        $tipo = $_POST['tipo'];
        $password = trim($_POST['pass']);

        try {
            $result = $model->editarUsuario($idUsuario, $nombre, $email, $password, $tipo);

            if ($result) {
                echo json_encode(["success" => true, "message" => "Usuario actualizado correctamente"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar usuario"]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        break;

    case 'eliminarUsuario':
        $idUsuario = $_POST['ID_Usuario'];

        try {
            $result = $model->eliminarUsuario($idUsuario);

            if ($result) {
                echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar usuario"]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        break;

    case 'getUsuario':
        $idUsuario = $_POST['ID_Usuario'];

        try {
            $usuario = $model->getUsuario($idUsuario);

            echo json_encode([
                "success" => true,
                "data" => $usuario
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida"]);
        break;
}
?>
