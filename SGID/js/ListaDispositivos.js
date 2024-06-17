$(document).ready(function() {
    var table;
    var dispositivos = [];
    
    function initializeQuill(selector, placeholder) {
        return new Quill(selector, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'font': [] }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            },
            placeholder: placeholder
        });
    }

    var quill = initializeQuill('#descripcionEditor', 'Escribe la descripción aquí...');
    var quillEdit = initializeQuill('#editDescripcionEditor', 'Escribe la descripción aquí...');
    var quillReparado = initializeQuill('#reparadoDescripcionEditor', 'Escribe la descripción aquí...');
    var quillProblema = initializeQuill('#problemaDescripcionEditor', 'Escribe la descripción aquí...');

    function clearBackdrop() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    }

    $('#numSerie, #editNumSerie').on('input', function() {
        var numSerie = $(this).val().trim();
        var isEdit = $(this).attr('id') === 'editNumSerie';
        var alertSelector = isEdit ? '#editAlertNumeroSerie' : '#alertNumeroSerie';
        var formSelector = isEdit ? '#formEditarDispositivo' : '#formAgregarDispositivo';
    
        if (numSerie.length === 0 || numSerie.length > 50) {
            $(alertSelector).text('El número de serie no puede estar vacío, contener solo espacios ni superar los 50 caracteres.').show();
            $(formSelector + ' button[type="submit"]').prop('disabled', true);
        } else {
            $.ajax({
                url: '../controllers/DispositivoController.php?action=checkNumSerie',
                method: 'POST',
                data: { num_serie: numSerie },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        $(alertSelector).text('El número de serie ya está registrado en estado "En espera".').show();
                        $(formSelector + ' button[type="submit"]').prop('disabled', true);
                    } else {
                        $(alertSelector).hide();
                        $(formSelector + ' button[type="submit"]').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la validación del número de serie:', error);
                }
            });
        }
    });

    function validarCamposFormulario(formSelector, quillInstance, alertModelo, alertDescripcion) {
        var modelo = $(`${formSelector} input[name="modelo"]`).val().trim();
        var descripcionText = quillInstance.getText().trim();
        var numSerie = $(`${formSelector} input[name="num_serie"]`).val().trim();

        var modeloValido = modelo.length > 0 && modelo.length <= 50;
        var descripcionValida = descripcionText.length > 0 && quillInstance.root.innerHTML.trim() !== '<p><br></p>';
        var numSerieValido = numSerie.length > 0 && numSerie.length <= 50;

        $(alertModelo).toggle(!modeloValido);
        $(alertDescripcion).toggle(!descripcionValida);

        return modeloValido && descripcionValida && numSerieValido;
    }

    function initializeDataTable() {
        table = $('#tablaDispositivos').DataTable({
            "ajax": {
                "url": "../controllers/DispositivoController.php?action=getDispositivos",
                "type": "GET",
                "dataSrc": function(response) {
                    if (!response.data) {
                        console.error('Error en la respuesta JSON:', response);
                        $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                        return [];
                    }
                    dispositivos = response.data.filter(function(dispositivo) {
                        return dispositivo.Estado === 'En espera' || dispositivo.Estado === 'Reparado';
                    });
                    return dispositivos;
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
                {"data": "Modelo"},
                {"data": "Num_Serie"},
                {
                    "data": "Descripcion",
                    "render": function(data, type, row) {
                        return `<button class="btn btn-warning btn-descripcion" data-descripcion="${data.replace(/"/g, '&quot;')}" data-accion="${row.accion}" data-establecimiento="${row.NombreEstablecimiento}"><i class="bi bi-text-indent-left"></i> Ver</button>`;
                    }
                },
                {"data": "NombreMarca"},
                {
                    "data": "Estado",
                    "render": function(data) {
                        let icon = '';
                        let color = '';
                        switch (data) {
                            case 'En espera':
                                icon = 'bi bi-clock';
                                color = 'orange';
                                break;
                            case 'Reparado':
                                icon = 'bi bi-check-circle';
                                color = 'green';
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
                            <div class="button-group">
                                <button class="btn btn-primary btn-sm btn-editar" data-index="${index}">Editar</button>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-index="${index}">Eliminar</button>
                                <button class="btn btn-success btn-sm btn-reparado" data-index="${index}">Actualizar Estado</button>
                            </div>
                        `;
                    }
                }
            ],
            "paging": true,
            "lengthMenu": [5, 10, 20],
            "pageLength": 5,
            "order": [[1, 'desc']],
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
            "drawCallback": function(settings) {
                bindOptionButtons();
            }
        });
    }

    function bindOptionButtons() {
        $(document).off('click', '.btn-editar').on('click', '.btn-editar', function() {
            var index = $(this).data('index');
            editarDispositivo(index);
        });

        $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
            var index = $(this).data('index');
            confirmarEliminar(index);
        });

        $(document).off('click', '.btn-reparado').on('click', '.btn-reparado', function() {
            var index = $(this).data('index');
            abrirModalReparado(index);
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

    $('#formAgregarDispositivo').on('submit', function(e) {
        e.preventDefault();
        var numSerie = $('#numSerie').val().trim();
        var descripcionHtml = quill.root.innerHTML.trim();
        $('#descripcion').val(descripcionHtml);

        if (!validarCamposFormulario('#formAgregarDispositivo', quill, '#alertModelo', '#alertDescripcion')) {
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: '../controllers/DispositivoController.php?action=agregarDispositivo',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalAgregarDispositivo').modal('hide');
                    clearBackdrop();
                    $('#formAgregarDispositivo')[0].reset();
                    quill.root.innerHTML = '';
                    $('#mensajeExito').text('Dispositivo registrado con éxito.');
                    $('#modalMensaje').modal('show');
                    table.ajax.reload(null, false);
                } else {
                    $('#alertNumeroSerie').text(response.message).show();
                    console.error('Error al agregar dispositivo:', response.message);
                }
                if (response.exists) {
                    $('#alertNumeroSerie').text('El número de serie que ha ingresado tiene una reparación "En espera".').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al agregar dispositivo:', error);
                $('#alertNumeroSerie').text('Error al agregar dispositivo.').show();
            }
        });
    });

    $('#formEditarDispositivo').on('submit', function(e) {
        e.preventDefault();
        var descripcionHtml = quillEdit.root.innerHTML.trim();
        $('#editDescripcion').val(descripcionHtml);

        if (!validarCamposFormulario('#formEditarDispositivo', quillEdit, '#editAlertModelo', '#editAlertDescripcion')) {
            return;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: '../controllers/DispositivoController.php?action=editarDispositivo',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    table.ajax.reload(null, false);
                    $('#modalEditar').modal('hide');
                    clearBackdrop();
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
                $('#errorEditarMarca').text('Error al comunicarse con el servidor.');
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
                    $('#editAccion').val(dispositivo.accion);
                    quillEdit.root.innerHTML = dispositivo.Descripcion;
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
        $.ajax({
            url: '../controllers/DispositivoController.php?action=eliminarDispositivo',
            method: 'POST',
            data: { id: dispositivo.ID_Dispositivo },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    table.ajax.reload(null, false);
                    $('#modalConfirmacion').modal('hide');
                    clearBackdrop();
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
        $('#estado').val(dispositivo.Estado);

        if (dispositivo.Estado === 'Reparado') {
            $('#divReparacion').show();
            $('#divProblema').hide();
            quillReparado.root.innerHTML = dispositivo.DescripcionReparacion || '';
            quillProblema.root.innerHTML = '';
        } else if (dispositivo.Estado === 'No Reparado') {
            $('#divReparacion').hide();
            $('#divProblema').show();
            quillProblema.root.innerHTML = dispositivo.DescripcionProblema || '';
            quillReparado.root.innerHTML = '';
        } else {
            $('#divReparacion').hide();
            $('#divProblema').hide();
            quillReparado.root.innerHTML = '';
            quillProblema.root.innerHTML = '';
        }

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
        } else if ($('#estado').val() === 'No Reparado') {
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
                if (response.success) {
                    table.ajax.reload(null, false);
                    $('#modalReparado').modal('hide');
                    clearBackdrop();
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
        var accion = $(this).data('accion') ? `<strong>Acción:</strong> ${$(this).data('accion')}<br>` : '';
        var establecimiento = $(this).data('establecimiento') ? `<strong>Establecimiento:</strong> ${$(this).data('establecimiento')}<br>` : '';
        
        $('#descripcionCompleta').html(`${accion}${establecimiento}${descripcion}`);
        $('#modalDescripcion').modal('show');
    });

    $('.modal').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    initializeDataTable();
    cargarOpciones();
});
