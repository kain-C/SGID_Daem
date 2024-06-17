$(document).ready(function() {
    var table;

    // Función para inicializar la DataTable
    function initializeDataTable() {
        table = $('#tablaMarcas').DataTable({
            "ajax": {
                "url": "../controllers/MarcaController.php?action=getMarcas",
                "type": "POST",
                "dataSrc": "data" // Aquí especificamos la clave del objeto que contiene los datos
            },
            "paging": true,
            "lengthMenu": [5, 10, 20],
            "pageLength": 5,
            "order": [[0, 'asc']],
            "searching": true,
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
            },
            "columns": [
                { "data": "id" },
                { "data": "nombre" },
                { 
                    "data": null,
                    "defaultContent": `
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <button type="button" class="btn btn-warning btn-edit">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `,
                    "orderable": false
                }
            ],
            "order": [[0, 'asc']]
        });
    }

    // Inicializar DataTable
    initializeDataTable();

    // Función para validar campos vacíos o con solo espacios
    function validateField(value) {
        return value.trim().length > 0;
    }

    // Agregar nueva marca
    $('#formAgregarMarca').on('submit', function(e) {
        e.preventDefault();
        var nombre = $('#nombreMarca').val().trim();
    
        if (!validateField(nombre)) {
            $('#errorAgregarMarca').text('El nombre no puede estar vacío o contener solo espacios.');
            return;
        }
    
        var duplicado = false;
    
        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (data.nombre === nombre) {
                duplicado = true;
                return false; // Break loop
            }
        });
    
        if (duplicado) {
            $('#errorAgregarMarca').text('La marca ya existe.');
            return;
        }
    
        $.ajax({
            url: '../controllers/MarcaController.php?action=agregarMarca',
            type: 'POST',
            data: { nombre: nombre },
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.success) {
                    $('#modalAgregarMarca').modal('hide');
                    $('#formAgregarMarca')[0].reset();
                    $('#mensajeExito').text('Marca registrada con éxito.');
                    $('#modalMensaje').modal('show');
                    table.ajax.reload();
                } else {
                    $('#errorAgregarMarca').text(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al agregar marca:', error);
                $('#errorAgregarMarca').text('Error al comunicarse con el servidor.');
            }
        });
    });

    // Limpiar backdrop al cerrar modales manualmente
    function clearBackdrop() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    }

    // Editar marca
    $('#tablaMarcas tbody').on('click', 'button.btn-edit', function() {
        var data = table.row($(this).parents('tr')).data();
        $('#editIdMarca').val(data.id);
        $('#editNombreMarca').val(data.nombre);
        $('#modalEditarMarca').modal('show');
    });

    // Guardar cambios en la marca
    $('#formEditarMarca').on('submit', function(e) {
        e.preventDefault();
        var id = $('#editIdMarca').val().trim();
        var nombre = $('#editNombreMarca').val().trim();

        // Validar campo vacío o con solo espacios
        if (!validateField(id) || !validateField(nombre)) {
            $('#errorEditarMarca').text('El ID y el nombre no pueden estar vacíos.');
            return;
        }

        var duplicado = false;

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (data.id != id && data.nombre === nombre) {
                duplicado = true;
                return false; // Break loop
            }
        });

        if (duplicado) {
            $('#errorEditarMarca').text('La marca ya existe.');
            return;
        }

        $.ajax({
            url: '../controllers/MarcaController.php?action=editarMarca',
            type: 'POST',
            data: { id: id, nombre: nombre }, // Asegúrate de que los nombres coincidan
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.success) {
                    $('#modalEditarMarca').modal('hide');
                    clearBackdrop(); // Limpiar el backdrop manualmente
                    $('#formEditarMarca')[0].reset(); // Resetea el formulario
                    $('#mensajeExito').text('Marca actualizada con éxito.');
                    $('#modalMensaje').modal('show');
                    table.ajax.reload();
                } else {
                    $('#errorEditarMarca').text(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar marca:', error);
                $('#errorEditarMarca').text('Error al comunicarse con el servidor.');
            }
        });
    });

    // Eliminar marca
    $('#tablaMarcas tbody').on('click', 'button.btn-delete', function() {
        var data = table.row($(this).parents('tr')).data();
        $('#mensajeConfirmacion').text('¿Estás seguro de que deseas eliminar esta marca?');
        $('#modalConfirmacion').modal('show');

        $('#btnConfirmarEliminar').off('click').on('click', function() {
            $.ajax({
                url: '../controllers/MarcaController.php?action=eliminarMarca',
                type: 'POST',
                data: { id: data.id },
                success: function(response) {
                    console.log('Respuesta del servidor:', response); // Añadir este log para ver la respuesta
                    if (response.success) {
                        $('#modalConfirmacion').modal('hide');
                        clearBackdrop(); // Limpiar el backdrop manualmente
                        $('#mensajeExito').text('Marca eliminada con éxito.');
                        $('#modalMensaje').modal('show');
                        table.ajax.reload();
                    } else {
                        $('#modalConfirmacion').modal('hide');
                        clearBackdrop();
                        $('#mensajeError').text(response.message);
                        $('#modalError').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar marca:', error);
                    $('#errorEliminarMarca').text('Error al comunicarse con el servidor.');
                }
            });
        });
    });

    // Limpiar backdrop al cerrar modales de éxito y confirmación
    $('#modalMensaje').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    $('#modalConfirmacion').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    // Limpiar backdrop al cerrar modal de error
    $('#modalError').on('hidden.bs.modal', function () {
        clearBackdrop();
    });
});
