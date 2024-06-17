$(document).ready(function() {
    var table = $('#tablaUsuarios').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: '../controllers/usuarioController.php?action=getUsuarios',
            type: 'POST',
            error: function (xhr, error, thrown) {
                showMessageModal("Error al cargar datos: " + xhr.responseText, 'Error');
            }
        },
        "columns": [
            {
                data: null,
                render: function(data, type, row) {
                    return '<div class="user-initials">' + row.NombreUsuario.charAt(0).toUpperCase() + '</div>';
                }
            },
            { data: 'ID_Usuario' },
            { data: 'NombreUsuario' },
            { data: 'CorreoElectronico' },
            { data: 'tipo' },
            {
                data: null,
                render: function(data, type, row) {
                    var editButton = '<button class="btn btn-primary btn-edit" onclick="editarUsuario(' + row.ID_Usuario + ')">Editar</button>';
                    var deleteButton = row.tipo === 'usuarioRegular' ? '<button class="btn btn-danger btn-delete" onclick="eliminarUsuario(' + row.ID_Usuario + ')">Eliminar</button>' : 'Acceso Restringido';
                    return editButton + ' ' + deleteButton;
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json",
            "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    $('#formAgregarUsuario').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../controllers/usuarioController.php?action=agregarUsuario',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#modalAgregarUsuario').modal('hide');
                    table.ajax.reload(null, false); // No alterar la posición de la tabla
                    showMessageModal(response.message, 'Éxito');
                } else {
                    showMessageModal(response.message, 'Error');
                }
            },
            error: function(xhr, status, error) {
                showMessageModal('Error al registrar usuario: ' + xhr.responseText, 'Error');
            }
        });
    });

    $('#formEditarUsuario').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../controllers/usuarioController.php?action=editarUsuario',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#modalEditarUsuario').modal('hide');
                    table.ajax.reload(null, false); // No alterar la posición de la tabla
                    showMessageModal(response.message, 'Éxito');
                } else {
                    showMessageModal(response.message, 'Error');
                }
            },
            error: function(xhr, status, error) {
                showMessageModal('Error al editar usuario: ' + xhr.responseText, 'Error');
            }
        });
    });

    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    function showMessageModal(message, title) {
        $('#messageModalLabel').text(title);
        $('#messageModalBody').text(message);
        $('#messageModal').modal('show');
    }

    window.editarUsuario = function(idUsuario) {
        $.ajax({
            url: '../controllers/usuarioController.php?action=getUsuario',
            type: 'POST',
            data: { ID_Usuario: idUsuario },
            success: function(response) {
                if (response.success) {
                    var usuario = response.data;
                    $('#editIDUsuario').val(usuario.ID_Usuario);
                    $('#editNombreUsuario').val(usuario.NombreUsuario);
                    $('#editCorreoElectronico').val(usuario.CorreoElectronico);
                    $('#editTipo').val(usuario.tipo);
                    $('#editPass').attr("placeholder", "Dejar en blanco para no cambiar");
                    $('#editPass').val(''); // Deja el campo de contraseña vacío
                    $('#modalEditarUsuario').modal('show');
                } else {
                    showMessageModal(response.message, 'Error');
                }
            },
            error: function(xhr, status, error) {
                showMessageModal('Error al obtener datos del usuario: ' + xhr.responseText, 'Error');
            }
        });
    }

    window.eliminarUsuario = function(idUsuario) {
        if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
            $.ajax({
                url: '../controllers/usuarioController.php?action=eliminarUsuario',
                type: 'POST',
                data: { ID_Usuario: idUsuario },
                success: function(response) {
                    if (response.success) {
                        $('#tablaUsuarios').DataTable().ajax.reload(null, false); // No alterar la posición de la tabla
                        showMessageModal(response.message, 'Éxito');
                    } else {
                        showMessageModal(response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    showMessageModal('Error al eliminar usuario: ' + xhr.responseText, 'Error');
                }
            });
        }
    }
});
