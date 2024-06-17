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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css"> <!-- Enlace al archivo CSS -->
    <style>
        .card {
            margin-top: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
        .modal-dialog-wide {
            max-width: 80%;
        }
        .breadcrumb {
            background-color: #f8f9fa;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .modal-body .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .estado-icon {
            font-size: 1.5em; /* Aumenta el tamaño de los iconos */
        }
        .estado-icon-reparado {
            color: green; /* Color verde para iconos reparados */
        }
        .estado-icon-no-reparado {
            color: red; /* Color rojo para iconos no reparados */
        }
    </style>
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
                                <li class="breadcrumb-item active" aria-current="page">Gestión Dispositivos</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Gestión de Seguimiento   
                        </div>
                        <div class="card-description mt-3">
                            <div class="alert alert-info" role="alert">
                                Aquí puedes gestionar y hacer seguimiento de los dispositivos de la empresa. Utiliza las acciones disponibles para ver más detalles o historial de mantenimiento.
                            </div>
                        </div>
                        <div class="table-container table-responsive">
                            <table id="tablaHistorialDispositivos" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo de Dispositivo</th>
                                        <th>Modelo</th>
                                        <th>Número de Serie</th>
                                        <th>Marca</th>
                                        <th>Establecimiento</th>
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

<!-- Modal de Historial -->
<div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-wide">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialLabel">Historial de Mantenimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tablaHistorialMantenimiento" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha de Registro</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
<script src="../js/ListaSeguimiento.js"></script> 


</body>
</html>
