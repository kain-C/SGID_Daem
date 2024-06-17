<?php
include '../controllers/session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Completa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: #fff;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card:nth-child(1) {
            background-color: #17a2b8;
        }
        .card:nth-child(2) {
            background-color: #28a745;
        }
        .card:nth-child(3) {
            background-color: #dc3545;
        }
        .card-title {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 2em;
        }
        .card-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 3em;
            opacity: 0.3;
        }
        .card-btn {
            margin-top: 10px;
            background-color: #fff;
            color: #000;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .card-btn:hover {
            background-color: #e0e0e0;
        }
        .reports {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .report-chart {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: 400px;
        }
        .report-chart canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content-container">
        <div class="dashboard">
            <div class="card">
                <i class="bi bi-clock-history card-icon"></i>
                <div class="card-body">
                    <h5 class="card-title">Dispositivos en Espera</h5>
                    <p class="card-text" id="enEspera-count">0</p>
                    <button class="card-btn" onclick="location.href='ruta_a_vista_en_espera.php'">Ver Detalles</button>
                </div>
            </div>
            <div class="card">
                <i class="bi bi-check-circle card-icon"></i>
                <div class="card-body">
                    <h5 class="card-title">Dispositivos Reparados</h5>
                    <p class="card-text" id="reparados-count">0</p>
                    <button class="card-btn" onclick="location.href='ruta_a_vista_reparados.php'">Ver Detalles</button>
                </div>
            </div>
            <div class="card">
                <i class="bi bi-x-circle card-icon"></i>
                <div class="card-body">
                    <h5 class="card-title">Dispositivos No Reparados</h5>
                    <p class="card-text" id="noReparados-count">0</p>
                    <button class="card-btn" onclick="location.href='ruta_a_vista_no_reparados.php'">Ver Detalles</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/vistaGrafics.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
</body>
</html>

