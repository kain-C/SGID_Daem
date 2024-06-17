$(document).ready(function() {
    var dispositivos = [];
    var quill = new Quill('#descripcionEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Escribe la descripción aquí...'
    });

    var quillEdit = new Quill('#editDescripcionEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Escribe la descripción aquí...'
    });

    var quillReparado = new Quill('#reparadoDescripcionEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Escribe la descripción de la reparación aquí...'
    });

    var quillProblema = new Quill('#problemaDescripcionEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Escribe la descripción del problema aquí...'
    });

    $('#numSerie, #editNumSerie').on('input', function() {
        var numSerie = $(this).val();
        var isEdit = $(this).attr('id') === 'editNumSerie';
        var alertSelector = isEdit ? '#editAlertNumeroSerie' : '#alertNumeroSerie';
        var formSelector = isEdit ? '#formEditarDispositivo' : '#formAgregarDispositivo';
        if (numSerie.length > 50) {
            $(alertSelector).text('El número de serie no puede superar los 50 caracteres.').show();
            $(formSelector + ' button[type="submit"]').prop('disabled', true);
        } else {
            $(alertSelector).hide();
            $(formSelector + ' button[type="submit"]').prop('disabled', false);
        }
    });

    function renderizarTabla() {
        if ($.fn.DataTable.isDataTable('#tablaDispositivosNoReparados')) {
            $('#tablaDispositivosNoReparados').DataTable().clear().destroy();
        }

        $('#tablaDispositivosNoReparados').DataTable({
            "ajax": {
                "url": "../controllers/DispositivoController.php?action=getDispositivosNoReparados",
                "type": "GET",
                "dataSrc": function(response) {
                    console.log('Respuesta de dispositivos:', response);
                    if (!response.data) {
                        console.error('Error en la respuesta JSON:', response);
                        $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                        return [];
                    }
                    dispositivos = response.data;
                    return response.data;
                },
                "error": function(xhr, error, thrown) {
                    console.error('Error en la solicitud AJAX:', error);
                    $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                }
            },
            "columns": [
                {"data": "ID_Dispositivo"},
                {"data": "FechaRegistro"},
                {"data": "Tipo_dispositivo"},
                {"data": "accion"},
                {"data": "Modelo"},
                {"data": "Num_Serie"},
                {
                    "data": "Descripcion",
                    "render": function(data) {
                        return `<button class="btn-descripcion btn btn-warning" data-descripcion="${data.replace(/"/g, '&quot;')}"><i class="bi bi-text-indent-left"></i> Ver</button>`;
                    }
                },
                {"data": "NombreMarca"},
                {"data": "NombreEstablecimiento"},
                {
                    "data": "Estado",
                    "render": function(data) {
                        let icon = '';
                        let color = '';
                        switch (data) {
                            case 'En Espera':
                                icon = 'bi bi-clock';
                                color = 'yellow';
                                break;
                            case 'Reparado':
                                icon = 'bi bi-check-circle';
                                color = 'green';
                                break;
                            case 'No Reparado':
                                icon = 'bi bi-x-circle';
                                color = 'red';
                                break;
                            default:
                                icon = 'bi bi-question-circle';
                                color = 'grey';
                        }
                        return `<span class="estado-icon" style="font-size: 1.5em; color: ${color};" title="${data}"><i class="${icon}"></i></span>`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        var index = meta.row;
                        return `
                            <button class="btn-actualizar-estado btn btn-info" onclick="abrirModalReparado(${index})">
                                <i class="bi bi-pencil-square"></i> Actualizar Estado
                            </button>
                        `;
                    }
                }
            ],
            "paging": true,
            "lengthMenu": [5, 10, 20],
            "pageLength": 5,
            "order": [[0, 'asc']],
            "searching": true,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
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
            "dom": '<"top"fl>rt<"bottom"ip><"clear">',
            "responsive": true,
            "ordering": false  // Desactivar la ordenación
        });
    }

    function cargarOpciones(callback) {
        var marcasCargadas = false;
        var establecimientosCargados = false;

        $.ajax({
            url: '../controllers/DispositivoController.php?action=getMarcas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de marcas:', response);
                if (response.data && Array.isArray(response.data)) {
                    var select = $('#marcaId, #editMarca');
                    select.empty();
                    select.append('<option value="">Selecciona una marca</option>');
                    response.data.forEach(function(marca) {
                        select.append('<option value="' + marca.id + '">' + marca.nombre + '</option>');
                    });
                }
                marcasCargadas = true;
                if (marcasCargadas && establecimientosCargados && callback) callback();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar las marcas:', error);
                $('#alertaError').text('Error al cargar las marcas. Por favor, inténtalo de nuevo.').show();
            }
        });

        $.ajax({
            url: '../controllers/DispositivoController.php?action=getEstablecimientos',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de establecimientos:', response);
                if (response.data && Array.isArray(response.data)) {
                    var select = $('#establecimientoId, #editEstablecimiento');
                    select.empty();
                    select.append('<option value="">Selecciona un establecimiento</option>');
                    response.data.forEach(function(establecimiento) {
                        select.append('<option value="' + establecimiento.ID_Establecimiento + '">' + establecimiento.NombreEstablecimiento + '</option>');
                    });
                }
                establecimientosCargados = true;
                if (marcasCargadas && establecimientosCargados && callback) callback();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar los establecimientos:', error);
                $('#alertaError').text('Error al cargar los establecimientos. Por favor, inténtalo de nuevo.').show();
            }
        });
    }

    $('#formEditarDispositivo').on('submit', function(e) {
        e.preventDefault();
        var numSerie = $('#editNumSerie').val().trim();
        if (numSerie.length > 50) {
            $('#editAlertNumeroSerie').text('El número de serie no puede superar los 50 caracteres.').show();
            return;
        }
        $('#editAlertNumeroSerie').hide();
        var descripcionHtml = quillEdit.root.innerHTML.trim();
        $('#editDescripcion').val(descripcionHtml);

        var formData = $(this).serialize();

        $.ajax({
            url: '../controllers/DispositivoController.php?action=editarDispositivo',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarTabla();
                    $('#modalEditar').modal('hide');
                    $('#formEditarDispositivo')[0].reset();
                    quillEdit.root.innerHTML = '';
                    $('#mensajeExito').text('Dispositivo actualizado con éxito.');
                    $('#modalMensaje').modal('show');
                } else {
                    console.error('Error al editar dispositivo:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar dispositivo:', error);
            }
        });
    });

    window.editarDispositivo = function(index) {
        var dispositivo = dispositivos[index];
        $.ajax({
            url: '../controllers/DispositivoController.php?action=getDispositivo',
            method: 'GET',
            data: { id: dispositivo.ID_Dispositivo },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var dispositivo = response.data;
                    $('#editIdDispositivo').val(dispositivo.ID_Dispositivo);
                    $('#editTipoDispositivo').val(dispositivo.Tipo_dispositivo);
                    $('#editModelo').val(dispositivo.Modelo);
                    $('#editNumSerie').val(dispositivo.Num_Serie);
                    $('#editAccion').val(dispositivo.accion); // Asegúrate de que el campo "acción" se rellene correctamente
                    quillEdit.root.innerHTML = dispositivo.Descripcion || ''; // Evitar undefined
                    $('#editMarca').val(dispositivo.marca_id);
                    $('#editEstablecimiento').val(dispositivo.ID_Establecimiento);
                    $('#modalEditar').modal('show');
                } else {
                    console.error('Error al obtener los detalles del dispositivo:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    };

    window.confirmarEliminar = function(index) {
        var dispositivo = dispositivos[index];
        $('#mensajeConfirmacion').text('¿Estás seguro de que deseas eliminar este dispositivo?');
        $('#btnConfirmarEliminar').off('click').on('click', function() {
            eliminarDispositivo(index);
        });
        $('#modalConfirmacion').modal('show');
    };

    function eliminarDispositivo(index) {
        var dispositivo = dispositivos[index];
        console.log('Intentando eliminar dispositivo:', dispositivo); // Depuración
        $.ajax({
            url: '../controllers/DispositivoController.php?action=eliminarDispositivo',
            method: 'POST',
            data: { id: dispositivo.ID_Dispositivo },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response); // Depuración
                if (response.success) {
                    renderizarTabla();
                    $('#modalConfirmacion').modal('hide');
                    $('#mensajeExito').text('Dispositivo eliminado con éxito.');
                    $('#modalMensaje').modal('show');
                } else {
                    console.error('Error al eliminar dispositivo:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al eliminar dispositivo:', error);
            }
        });
    }

    window.abrirModalReparado = function(index) {
        var dispositivo = dispositivos[index];
        $('#reparadoIdDispositivo').val(dispositivo.ID_Dispositivo);
        $('#estado').val('No Reparado'); // Mostrar el estado "No Reparado" por defecto
        $('#divReparacion').hide();
        $('#divProblema').show();
        quillProblema.root.innerHTML = dispositivo.DescripcionProblema ? dispositivo.DescripcionProblema : '';
        $('#modalReparado').modal('show');
    };

    $('#estado').on('change', function() {
        if ($(this).val() === 'Reparado') {
            $('#divReparacion').show();
            $('#divProblema').hide();
            quillProblema.root.innerHTML = '';
        } else {
            $('#divReparacion').hide();
            $('#divProblema').show();
            quillReparado.root.innerHTML = '';
        }
    });

    $('#formReparado').on('submit', function(e) {
        e.preventDefault();
        var descripcionHtml = quillReparado.root.innerHTML.trim();
        var problemaHtml = quillProblema.root.innerHTML.trim();

        if ($('#estado').val() === 'Reparado') {
            $('#reparadoDescripcion').val(descripcionHtml);
            $('#problemaDescripcion').val('');
        } else {
            $('#reparadoDescripcion').val('');
            $('#problemaDescripcion').val(problemaHtml);
        }

        var formData = $(this).serialize();

        $.ajax({
            url: '../controllers/DispositivoController.php?action=repararDispositivo',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response); // Depuración
                if (response.success) {
                    renderizarTabla();
                    $('#modalReparado').modal('hide');
                    $('#mensajeExito').text('El dispositivo ha sido actualizado con éxito.');
                    $('#modalMensaje').modal('show');
                } else {
                    console.error('Error al actualizar el estado del dispositivo:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar el estado del dispositivo:', error);
            }
        });
    });

    window.verHistorial = function(idDispositivo) {
        $.ajax({
            url: '../controllers/DispositivoController.php?action=obtenerHistorialMantenimiento',
            method: 'GET',
            data: { id_dispositivo: idDispositivo },
            dataType: 'json',
            success: function(response) {
                if (response.data) {
                    let historialHtml = '<ul class="list-group">';
                    response.data.forEach(item => {
                        historialHtml += `<li class="list-group-item">
                            <strong>Fecha:</strong> ${item.Fecha} <br>
                            <strong>Usuario:</strong> ${item.NombreUsuario} <br>
                            <strong>Descripción:</strong> ${item.Descripcion} <br>
                            <strong>Tipo:</strong> ${item.tipo}
                        </li>`;
                    });
                    historialHtml += '</ul>';
                    $('#historialContainer').html(historialHtml);
                    $('#modalHistorial').modal('show');
                } else {
                    console.error('Error al obtener el historial:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener el historial:', error);
            }
        });
    };

    $(document).on('click', '.btn-descripcion', function () {
        var descripcion = $(this).data('descripcion');
        $('#descripcionCompleta').html(descripcion);
        $('#modalDescripcion').modal('show');
    });

    renderizarTabla();
    cargarOpciones();
});
