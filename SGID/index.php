<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: view/viewPageHome.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="icon" href="/path/to/favicon.ico" type="image/x-icon">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('./img/fondo1.jpg'); /* Ruta de tu imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Para que la imagen de fondo permanezca fija mientras se desplaza la página */
        }

        #loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
            display: none; /* Ocultar la rueda de carga inicialmente */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .card-login {
            width: 400px;
            margin: 150px auto;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 40px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="card-login">
                <h1 class="text-center mb-4">Iniciar Sesión</h1>
                <form id="loginForm" action="../controllers/LoginController.php" method="POST">
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </form>
                <div id="loader"></div>
                <div id="mensaje"></div>
            </div>
        </div>
    </div>

    <script >
        $(document).ready(function(){
    $('#loginForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'controllers/LoginController.php',
            data: $(this).serialize(),
            success: function(response){
                $('#mensaje').html(response);
                if (response.includes('exitoso')) {
                    setTimeout(function(){
                        window.location.href = 'view/viewPageHome.php'; // Redireccionar a la página viewHome.php después de 2 segundos
                    }, 2000); // 2000 milisegundos = 2 segundos
                }
            }
        });
    });
});
    </script>
</body>
</html>