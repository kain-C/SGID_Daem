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