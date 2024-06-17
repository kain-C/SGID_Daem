<?php
include '../controllers/session.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reparaciones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
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
                                <li class="breadcrumb-item active" aria-current="page">Gestión Reparaciones</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión de Reparaciones
                        </div>
                        <div class="alert alert-primary" role="alert">
                        Gestion de los dispositivos que ya han sido reparados, podras generar informe de los dispositivos, etiquetarlos o editarlos,actualiza el estado segun corresponda.
                        </div>

                        <div class="table-responsive">
                            <table id="reparaciones-table" class="table table-striped table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>#</th> 
                                        <th>ID</th>
                                        <th>Fecha de Registro</th>
                                        <th>Descripcion</th>
                                        <th>Modelo</th>
                                        <th>N°</th>
                                        <th>Establecimiento</th>
                                        <th>Marca</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- filas cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar las descripciones -->
<div class="modal fade" id="descripcionModal" tabindex="-1" role="dialog" aria-labelledby="descripcionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descripcionModalLabel">Descripción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="descripcion-content"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para QR -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-qr-code"></i> Generar QR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <div class="card border-dark mb-3 qr-card">
                            <div class="card-body">
                                <div id="qrcode-container" class="qr-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="reparacion-seleccionada" class="mb-3"></div>
                        <div class="form-group">
                            <label for="qrQuantity">Cantidad de QR:</label>
                            <input type="number" id="qrQuantity" class="form-control" min="1" value="1">
                        </div>
                        <div class="form-group">
                            <label for="qrSize">Tamaño del QR (cm):</label>
                            <input type="number" id="qrSize" class="form-control" min="1" value="3">
                        </div>
                        <div class="form-group">
                            <label>Posición del QR:</label>
                            <div class="form-check">
                                <input class="form-check-input qr-position" type="checkbox" name="qrPosition" id="positionStart" value="start">
                                <label class="form-check-label" for="positionStart">Al inicio</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input qr-position" type="checkbox" name="qrPosition" id="positionMiddle" value="middle">
                                <label class="form-check-label" for="positionMiddle">Al medio</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input qr-position" type="checkbox" name="qrPosition" id="positionEnd" value="end">
                                <label class="form-check-label" for="positionEnd">Al final</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="save-qrcode-btn" class="btn btn-primary">Guardar como Imagen</button>
                <button id="generate-pdf-btn" class="btn btn-primary">Generar PDF</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar la descripción de la reparación -->
<div class="modal fade" id="editReparacionModal" tabindex="-1" role="dialog" aria-labelledby="editReparacionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReparacionModalLabel">Editar Descripción de Reparación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-reparacion-form">
                    <div class="form-group">
                        <label for="editDescripcionReparacion">Descripción de Reparación</label>
                        <textarea class="form-control" id="editDescripcionReparacion" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="editReparacionId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveEditReparacionBtn">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="../js/ListaReparacion.js"></script>
    <script>
    // Pasamos el nombre del usuario a una variable JavaScript
    var nombreUsuario = "<?php echo $nombreUsuario; ?>";
</script>
</body>
</html>
