<?php
include '../controllers/session.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dispositivos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-container">
        <div class="content">
            <div class="row">
                <div class="col-12 mb-4">
                    <button class="btn btn-primary" id="btnAgregarRecordatorio">Agregar Recordatorio</button>
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal para agregar recordatorio -->
    <div class="modal fade" id="agregarRecordatorioModal" tabindex="-1" aria-labelledby="agregarRecordatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarRecordatorioModalLabel">Agregar Recordatorio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="agregarRecordatorioForm">
                        <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo isset($_SESSION['ID_Usuario']) ? $_SESSION['ID_Usuario'] : ''; ?>">
                        <div class="form-group">
                            <label for="titulo">Título del Recordatorio:</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="hora">Hora:</label>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para modificar recordatorio -->
    <div class="modal fade" id="modificarRecordatorioModal" tabindex="-1" aria-labelledby="modificarRecordatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modificarRecordatorioModalLabel">Modificar Recordatorio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modificarRecordatorioForm">
                        <input type="hidden" id="recordatorio_id_modificar" name="id">
                        <div class="form-group">
                            <label for="usuario_nombre_modificar">Usuario que creó el recordatorio:</label>
                            <input type="text" class="form-control" id="usuario_nombre_modificar" name="usuario_nombre_modificar" readonly>
                        </div>
                        <div class="form-group">
                            <label for="titulo_modificar">Título del Recordatorio:</label>
                            <input type="text" class="form-control" id="titulo_modificar" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_modificar">Descripción:</label>
                            <textarea class="form-control" id="descripcion_modificar" name="descripcion"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="fecha_modificar">Fecha:</label>
                            <input type="date" class="form-control" id="fecha_modificar" name="fecha" required>
                        </div>
                        <div class="form-group">
                            <label for="hora_modificar">Hora:</label>
                            <input type="time" class="form-control" id="hora_modificar" name="hora" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-danger" id="eliminarRecordatorio" style="display: none;">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para visualizar recordatorio -->
    <div class="modal fade" id="visualizarRecordatorioModal" tabindex="-1" aria-labelledby="visualizarRecordatorioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="visualizarRecordatorioModalLabel">Detalles del Recordatorio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Usuario:</strong> <span id="visualizarUsuarioNombre"></span></p>
                    <p><strong>Título:</strong> <span id="visualizarTitulo"></span></p>
                    <p><strong>Descripción:</strong> <span id="visualizarDescripcion"></span></p>
                    <p><strong>Fecha y Hora:</strong> <span id="visualizarFecha"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="../js/calendario.js"></script>
</body>
</html>
