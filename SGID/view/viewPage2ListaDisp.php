<?php
include '../controllers/session.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Dispositivos</title>
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
                                <li class="breadcrumb-item active" aria-current="page">Gestión de Dispositivos</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión de Dispositivos
                        </div>
                        <div class="card-description">
                            <div class="alert alert-info" role="alert">
                                Aquí podrás gestionar los dispositivos de la empresa. Puedes agregar nuevos dispositivos, editar sus detalles, Cambia el estado del dispositivo para ingresarlo a Reparaciones.
                            </div>
                        </div>
                        <button class="btn btn-success btn-lg mb-3" data-toggle="modal" data-target="#modalAgregarDispositivo"><i class="bi bi-plus-lg"></i> Agregar Dispositivo</button>
                       
                        <div class="table-container table-responsive">
                            <table id="tablaDispositivos" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha de Registro</th>
                                        <th>Tipo de Dispositivo</th>
                                        <th>Modelo</th>
                                        <th>N°</th>
                                        <th>Descripción</th>
                                        <th>Marca</th>
                                        <th>Estado</th>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <input type="text" class="form-control" id="editModelo" name="modelo" maxlength="50" required>
                        <div id="editAlertModelo" class="alert alert-danger mt-2" style="display:none;">El modelo no puede contener solo espacios y debe tener un máximo de 50 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editNumSerie" class="form-label">Número de Serie:</label>
                        <input type="text" class="form-control" id="editNumSerie" name="num_serie" maxlength="50" required>
                        <div id="editAlertNumeroSerie" class="alert alert-danger mt-2" style="display:none;">El número de serie no puede estar vacío, contener solo espacios ni superar los 50 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editAccion" class="form-label">Acción:</label>
                        <select class="form-control" id="editAccion" name="accion" required>
                            <option value="Reparación">Reparación</option>
                            <option value="Mantenimiento Preventivo">Mantenimiento Preventivo</option>
                            <option value="Actualización de Software">Actualización de Software</option>
                            <option value="Sustitución de Componentes">Sustitución de Componentes</option>
                            <option value="Inspección Técnica">Inspección Técnica</option>
                            <option value="Calibración">Calibración</option>
                            <option value="Limpieza y Mantenimiento General">Limpieza y Mantenimiento General</option>
                            <option value="Diagnóstico">Diagnóstico</option>
                            <option value="Revisión de Seguridad">Revisión de Seguridad</option>
                            <option value="Reparación de Emergencia">Reparación de Emergencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDescripcionEditor" class="form-label">Descripción:</label>
                        <div id="editDescripcionEditor"></div>
                        <input type="hidden" id="editDescripcion" name="descripcion" required>
                        <div id="editAlertDescripcion" class="alert alert-danger mt-2" style="display:none;">La descripción no puede contener solo espacios.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editMarca" class="form-label">Marca:</label>
                        <select class="form-control" id="editMarca" name="marcaId"></select>
                    </div>
                    <div class="mb-3">
                        <label for="editEstablecimiento" class="form-label">Establecimiento:</label>
                        <select class="form-control" id="editEstablecimiento" name="establecimientoId"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Agregar Dispositivo -->
<div class="modal fade" id="modalAgregarDispositivo" tabindex="-1" aria-labelledby="modalAgregarDispositivoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wide">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarDispositivoLabel">Agregar Dispositivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAgregarDispositivo">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tipoDispositivo">Tipo de Dispositivo:</label>
                            <select class="form-control" id="tipoDispositivo" name="tipo_dispositivo" required>
                                <option value="">Seleccione un tipo...</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Smartphone">Smartphone</option>
                                <option value="Cámara">Cámara</option>
                                <option value="Consola">Consola</option>
                                <!-- Agrega más tipos según sea necesario -->
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modelo">Modelo:</label>
                            <small id="numSerieHelp" class="form-text text-muted">Máximo 50 caracteres.</small>
                            <input type="text" class="form-control" id="modelo" name="modelo" maxlength="50" required>
                            <div id="alertModelo" class="alert alert-danger mt-2" style="display:none;">El modelo no puede contener solo espacios y debe tener un máximo de 50 caracteres.</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="numSerie">Número de Serie:</label>
                            <input type="text" class="form-control" id="numSerie" name="num_serie" maxlength="50" required>
                            <small id="numSerieHelp" class="form-text text-muted">Máximo 50 caracteres.</small>
                            <div id="alertNumeroSerie" class="alert alert-danger mt-2" style="display: none;">
                                El número de serie no puede estar vacío, contener solo espacios ni superar los 50 caracteres.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="marcaId">Marca:</label>
                            <select class="form-control" id="marcaId" name="marcaId" required></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="establecimientoId">Establecimiento:</label>
                            <select class="form-control" id="establecimientoId" name="establecimientoId" required></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="accion">Acción:</label>
                            <select class="form-control" id="accion" name="accion" required>
                                <option value="Reparación">Reparación</option>
                                <option value="Mantenimiento Preventivo">Mantenimiento Preventivo</option>
                                <option value="Actualización de Software">Actualización de Software</option>
                                <option value="Sustitución de Componentes">Sustitución de Componentes</option>
                                <option value="Inspección Técnica">Inspección Técnica</option>
                                <option value="Calibración">Calibración</option>
                                <option value="Limpieza y Mantenimiento General">Limpieza y Mantenimiento General</option>
                                <option value="Diagnóstico">Diagnóstico</option>
                                <option value="Revisión de Seguridad">Revisión de Seguridad</option>
                                <option value="Reparación de Emergencia">Reparación de Emergencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcionEditor">Descripción:</label>
                        <div class="alert alert-info" role="alert">
                            Ingresa el estado actual del Dispositivo
                        </div>
                        <div id="descripcionEditor" class="form-control" style="height: 200px;"></div>
                        <input type="hidden" id="descripcion" name="descripcion" required>
                        <div id="alertDescripcion" class="alert alert-danger mt-2" style="display:none;">La descripción no puede contener solo espacios.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
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
<script src="../js/ListaDispositivos.js"></script>
</body>
</html>