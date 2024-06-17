<?php
include '../controllers/session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Dispositivos No Reparados</title>
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
                                <li class="breadcrumb-item"><a href="viewPageHome.php">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestión de Dispositivos No Reparados</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión de Dispositivos No Reparados    
                        </div>
                        <div class="table-responsive">
                            <table id="tablaDispositivosNoReparados" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Acción</th>
                                        <th>Modelo</th>
                                        <th>N°</th>
                                        <th>Descripción</th>
                                        <th>Marca</th>
                                        <th>Establecimiento</th>
                                        <th>Estado</th>
                                        <th></th>
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

<!-- Modal para Actualizar Estado del Dispositivo -->
<div class="modal fade" id="modalReparado" tabindex="-1" aria-labelledby="modalReparadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wide">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReparadoLabel">Actualizar Estado del Dispositivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="formReparado">
                    <input type="hidden" id="reparadoIdDispositivo" name="id">
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="">Seleccione un estado...</option>
                            <option value="Reparado">Reparado</option>
                            <option value="No Reparado">No Reparado</option>
                        </select>
                    </div>
                    <div class="form-group" id="divReparacion" style="display:none;">
                        <label for="reparadoDescripcion">Descripción de la Reparación:</label>
                        <div id="reparadoDescripcionEditor" class="form-control" style="height: 200px;"></div>
                        <input type="hidden" id="reparadoDescripcion" name="descripcion_reparacion">
                    </div>
                    <div class="form-group" id="divProblema" style="display: none;">
                        <label for="problemaDescripcion">Descripción del Problema:</label>
                        <div id="problemaDescripcionEditor" class="form-control" style="height: 200px;"></div>
                        <input type="hidden" id="problemaDescripcion" name="descripcion_problema">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Descripción -->
<div class="modal fade" id="modalDescripcion" tabindex="-1" aria-labelledby="modalDescripcionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wide">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDescripcionLabel">Descripción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="descripcionCompleta" class="quill-view"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacionLabel">Confirmación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p id="mensajeConfirmacion"></p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wide">
        <div class="modal-content">
            <form id="formEditarDispositivo">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editIdDispositivo" name="id">
                    <div class="mb-3">
                        <label for="editTipoDispositivo" class="form-label">Tipo de Dispositivo:</label>
                        <select class="form-control" id="editTipoDispositivo" name="tipo_dispositivo" required>
                            <option value="">Seleccione un tipo...</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Impresora">Impresora</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Smartphone">Smartphone</option>
                            <!-- Agrega más tipos según sea necesario -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editModelo" class="form-label">Modelo:</label>
                        <input type="text" class="form-control" id="editModelo" name="modelo">
                    </div>
                    <div class="mb-3">
                        <label for="editNumSerie" class="form-label">Número de Serie:</label>
                        <input type="text" class="form-control" id="editNumSerie" name="num_serie">
                        <div id="editAlertNumeroSerie" class="alert alert-danger mt-2" style="display:none;">El número de serie ya está registrado.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescripcionEditor" class="form-label">Descripción:</label>
                        <div id="editDescripcionEditor"></div>
                        <input type="hidden" id="editDescripcion" name="descripcion">
                    </div>
                    <div class="mb-3">
                        <label for="editMarca" class="form-label">Marca:</label>
                        <select class="form-select" id="editMarca" name="marcaId"></select>
                    </div>
                    <div class="mb-3">
                        <label for="editEstablecimiento" class="form-label">Establecimiento:</label>
                        <select class="form-select" id="editEstablecimiento" name="establecimientoId"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Mensaje de Éxito -->
<div class="modal fade" id="modalMensaje" tabindex="-1" aria-labelledby="modalMensajeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMensajeLabel">Mensaje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p id="mensajeExito"></p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
<script src="../js/ListaDispNR.js"></script>

</body>
</html>
