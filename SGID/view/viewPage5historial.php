<?php
include '../controllers/session.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Historial</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css"> <!-- Enlace al archivo CSS -->
    <style>
        .spinner-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
        }
        .spinner-grow {
            width: 3rem;
            height: 3rem;
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
                                <li class="breadcrumb-item active" aria-current="page">Historial</li>
                            </ol>
                        </nav>
                        <div class="card-header">
                            Historial
                        </div>
                        <div class="card-description">
                            <div class="alert alert-info" role="alert">
                                En esta vista, puedes gestionar los dispositivos de la empresa. Puedes agregar nuevos dispositivos, editar sus detalles.
                            </div>
                        </div>
                        <h2 class="text-center mb-4">Tabla de Historial</h2>
                        <div class="mb-4 text-center">
                            <label for="filtroHistorial" class="mr-2">Filtrar por:</label>
                            <select id="filtroHistorial" class="form-control d-inline-block" style="width: 200px;">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="mes">Último Mes</option>
                                <option value="año">Este Año</option>
                                <option value="todos" selected>Todos</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table id="tablaHistorial" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID Usuario</th>
                                        <th>Fecha y Hora</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="alert alert-danger mt-2" id="alertaError" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="../js/Listarhistorial.js"></script>

  
</body>
</html>
