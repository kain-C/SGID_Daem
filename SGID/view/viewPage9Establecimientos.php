<?php
include '../controllers/session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Establecimientos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
<?php include 'sidebar.php'; ?>
<!-- Modal para agregar establecimiento -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarLabel">Agregar Establecimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioEstablecimiento">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
                    </div>
                    <div class="form-group">
                        <label for="localidad">Localidad:</label>
                        <input type="text" class="form-control" id="localidad" name="localidad" placeholder="Localidad" required>
                    </div>
                    <div class="form-group">
                        <label for="rbd">RBD:</label>
                        <input type="text" class="form-control" id="rbd" name="rbd" placeholder="RBD" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Establecimiento:</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">Seleccione uno...</option>
                            <option value="Escuela">Escuela</option>
                            <option value="Jardín">Jardín</option>
                            <option value="Liceo">Liceo</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group" id="otroTipoEstablecimiento" style="display: none;">
                        <label for="otroTipo">Especifique:</label>
                        <input type="text" class="form-control" id="otroTipo" name="otroTipo" placeholder="Especifique">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Establecimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditarEstablecimiento">
                    <input type="hidden" id="editIdEstablecimiento" name="id">
                    <div class="form-group">
                        <label for="editNombre">Nombre:</label>
                        <input type="text" class="form-control" id="editNombre" name="nombre" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="editDireccion">Dirección:</label>
                        <input type="text" class="form-control" id="editDireccion" name="direccion" placeholder="Dirección" required>
                    </div>
                    <div class="form-group">
                        <label for="editLocalidad">Localidad:</label>
                        <input type="text" class="form-control" id="editLocalidad" name="localidad" placeholder="Localidad" required>
                    </div>
                    <div class="form-group">
                        <label for="editRbd">RBD:</label>
                        <input type="text" class="form-control" id="editRbd" name="rbd" placeholder="RBD" required>
                    </div>
                    <div class="form-group">
                        <label for="editTipo">Tipo de Establecimiento:</label>
                        <select class="form-control" id="editTipo" name="tipo">
                            <option value="">Seleccione uno...</option>
                            <option value="Escuela">Escuela</option>
                            <option value="Jardín">Jardín</option>
                            <option value="Liceo">Liceo</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group" id="editOtroTipoEstablecimiento" style="display: none;">
                        <label for="editOtroTipo">Especifique:</label>
                        <input type="text" class="form-control" id="editOtroTipo" name="otro_tipo" placeholder="Especifique">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal de eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">Eliminar Establecimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este establecimiento?</p>
                <input type="hidden" id="deleteIdEstablecimiento">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Éxito -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExitoLabel">Éxito</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                El establecimiento se ha agregado con éxito.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="content-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-3">
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="viewPageHome.php">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestión de Establecimientos</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión de Establecimientos
                        </div>
                        <div class="card-description">
                        <div class="alert alert-info" role="alert">
                                Aquí podrás gestionar los dispositivos de la empresa. Puedes agregar nuevos dispositivos, editar sus detalles, Cambia el estado del dispositivo para ingresarlo a Reparaciones.
                            </div>
                        </div>
                            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAgregar"><i class="bi bi-plus-lg"></i> Agregar Establecimiento</button>
                            <div class="table-container table-responsive">
                                <table id="tablaEstablecimientos" class="table table-striped table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th>Localidad</th>
                                            <th>RBD</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="../js/ListarEstablecimientos.js"></script>
</body>
</html>
