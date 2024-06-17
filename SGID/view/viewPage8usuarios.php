<?php
include '../controllers/session.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-3">
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestion Usuarios</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión Usuarios
                        </div>
                        <div class="card-description">
                            <div class="alert alert-info" role="alert">
                                Aquí podrás gestionar los dispositivos de la empresa. Puedes agregar nuevos dispositivos, editar sus detalles, Cambia el estado del dispositivo para ingresarlo a Reparaciones.
                            </div>
                        </div>
                        <button class="btn btn-success btn-lg mb-3" data-toggle="modal" data-target="#modalAgregarUsuario"><i class="bi bi-plus-lg"></i> Añadir Usuario</button>

                        <div class="table-container table-responsive">
                            <table id="tablaUsuarios" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID Usuario</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--carga dinamica-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Añadir Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAgregarUsuario">
                    <div class="form-group">
                        <label for="NombreUsuario">Nombre</label>
                        <input type="text" class="form-control" id="NombreUsuario" name="NombreUsuario" required pattern="\S+">
                    </div>
                    <div class="form-group">
                        <label for="CorreoElectronico">Email</label>
                        <input type="email" class="form-control" id="CorreoElectronico" name="CorreoElectronico" required pattern="\S+">
                    </div>
                    <div class="form-group position-relative">
                        <label for="pass">Contraseña</label>
                        <input type="password" class="form-control" id="pass" name="pass" required pattern="\S+">
                        <i class="fas fa-eye toggle-password" toggle="#pass"></i>
                    </div>
                    <div class="form-group position-relative">
                        <label for="confirm_pass">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required pattern="\S+">
                        <i class="fas fa-eye toggle-password" toggle="#confirm_pass"></i>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Rol</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">Seleccione uno...</option>
                            <option value="administrador">Administrador</option>
                            <option value="usuarioRegular">Usuario Regular</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarUsuario">
                    <input type="hidden" id="editIDUsuario" name="ID_Usuario">
                    <div class="form-group">
                        <label for="editNombreUsuario">Nombre</label>
                        <input type="text" class="form-control" id="editNombreUsuario" name="NombreUsuario" required pattern="\S+">
                    </div>
                    <div class="form-group">
                        <label for="editCorreoElectronico">Email</label>
                        <input type="email" class="form-control" id="editCorreoElectronico" name="CorreoElectronico" required pattern="\S+">
                    </div>
                    <div class="form-group position-relative">
                        <label for="editPass">Contraseña</label>
                        <input type="password" class="form-control" id="editPass" name="pass" pattern="\S+" placeholder="Dejar en blanco para no cambiar">
                        <i class="fas fa-eye toggle-password" toggle="#editPass"></i>
                    </div>
                    <div class="form-group">
                        <label for="editTipo">Rol</label>
                        <select class="form-control" id="editTipo" name="tipo" required>
                            <option value="">Seleccione uno...</option>
                            <option value="administrador">Administrador</option>
                            <option value="usuarioRegular">Usuario Regular</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="../js/ListaUsuarios.js"></script>
</body>
</html>
