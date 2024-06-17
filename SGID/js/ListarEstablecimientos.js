$(document).ready(function() {
    var table;

    function initializeDataTable() {
        table = $('#tablaEstablecimientos').DataTable({
            "ajax": {
                "url": "../controllers/establecimientos_controller.php",
                "type": "GET",
                "dataSrc": ""
            },
            "pagingType": "simple_numbers",
            "lengthMenu": [5, 10, 20, 50, 100],
            "pageLength": 10,
            "order": [[1, 'asc']],
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
                { "data": null, "defaultContent": "" },
                { "data": "NombreEstablecimiento" },
                { "data": "Direccion" },
                { "data": "Localidad" },
                { "data": "RBD" },
                { "data": "tipo_establecimiento" },
                {
                    "data": null,
                    "defaultContent": `
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <button type="button" class="btn btn-warning btn-edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    `,
                    "orderable": false
                }
            ],
            "order": [[1, 'asc']],
            "drawCallback": function(settings) {
                var api = this.api();
                api.column(0, {search: 'applied', order: 'applied'}).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });
    }

    initializeDataTable();

    $('#tipo').on('change', function () {
        if (this.value === 'Otro') {
            $('#otroTipoEstablecimiento').show();
            $('#otroTipo').prop('required', true);
        } else {
            $('#otroTipoEstablecimiento').hide();
            $('#otroTipo').prop('required', false);
        }
    });

    $('#editTipo').on('change', function () {
        if (this.value === 'Otro') {
            $('#editOtroTipoEstablecimiento').show();
            $('#editOtroTipo').prop('required', true);
        } else {
            $('#editOtroTipoEstablecimiento').hide();
            $('#editOtroTipo').prop('required', false);
        }
    });

    function validateForm(form) {
        var isValid = true;
        $(form).find('input[type="text"], select').each(function() {
            if ($(this).prop('required') && $.trim($(this).val()) === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }

    $('#formularioEstablecimiento').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm(this)) {
            alert('Por favor, complete todos los campos correctamente.');
            return;
        }

        var nombre = $('#nombre').val();
        var direccion = $('#direccion').val();
        var localidad = $('#localidad').val();
        var rbd = $('#rbd').val();
        var tipo = $('#tipo').val();
        var otroTipo = $('#otroTipo').val();
        var duplicado = false;

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (data.NombreEstablecimiento === nombre && data.Direccion === direccion &&
                data.Localidad === localidad && data.RBD === rbd && data.tipo_establecimiento === tipo) {
                duplicado = true;
                return false;
            }
        });

        if (duplicado) {
            alert('El establecimiento ya existe.');
            return;
        }

        $.ajax({
            url: '../controllers/establecimientos_controller.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $('#formularioEstablecimiento')[0].reset();
                    $('#otroTipoEstablecimiento').hide();
                    $('#modalAgregar').modal('hide');
                    $('#modalExito').modal('show');
                    table.ajax.reload(null, false);
                } else {
                    alert('Error: ' + response.error);
                }
            }
        });
    });

    $('#tablaEstablecimientos tbody').on('click', 'button.btn-edit', function () {
        var data = table.row($(this).parents('tr')).data();
        $('#editIdEstablecimiento').val(data.ID_Establecimiento);
        $('#editNombre').val(data.NombreEstablecimiento);
        $('#editDireccion').val(data.Direccion);
        $('#editLocalidad').val(data.Localidad);
        $('#editRbd').val(data.RBD);
        $('#editTipo').val(data.tipo_establecimiento).change();
        if (data.tipo_establecimiento === 'Otro') {
            $('#editOtroTipoEstablecimiento').show();
            $('#editOtroTipo').val(data.otro_tipo);
        } else {
            $('#editOtroTipoEstablecimiento').hide();
        }
        $('#modalEditar').modal('show');
    });

    $('#modalEditar').on('hidden.bs.modal', function () {
        $('#formEditarEstablecimiento')[0].reset();
        $('#editOtroTipoEstablecimiento').hide();
    });

    $('#formEditarEstablecimiento').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm(this)) {
            alert('Por favor, complete todos los campos correctamente.');
            return;
        }

        var id = $('#editIdEstablecimiento').val();
        var nombre = $('#editNombre').val();
        var direccion = $('#editDireccion').val();
        var localidad = $('#editLocalidad').val();
        var rbd = $('#editRbd').val();
        var tipo = $('#editTipo').val();
        var duplicado = false;

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (data.ID_Establecimiento != id && data.NombreEstablecimiento === nombre && data.Direccion === direccion &&
                data.Localidad === localidad && data.RBD === rbd && data.tipo_establecimiento === tipo) {
                duplicado = true;
                return false;
            }
        });

        if (duplicado) {
            alert('El establecimiento ya existe.');
            return;
        }

        var data = $(this).serialize() + '&accion=editar';
        $.ajax({
            url: '../controllers/establecimientos_controller.php',
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    $('#modalEditar').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    alert('Error: ' + response.error);
                }
            }
        });
    });

    $('#tablaEstablecimientos tbody').on('click', 'button.btn-delete', function () {
        var data = table.row($(this).parents('tr')).data();
        $('#deleteIdEstablecimiento').val(data.ID_Establecimiento);
        $('#modalEliminar').modal('show');
    });

    $('#confirmarEliminar').on('click', function () {
        var id = $('#deleteIdEstablecimiento').val();
        $.ajax({
            url: '../controllers/establecimientos_controller.php',
            type: 'POST',
            data: { id: id, accion: 'eliminar' },
            success: function () {
                $('#modalEliminar').modal('hide');
                table.ajax.reload(null, false);
            }
        });
    });

    function clearBackdrop() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    }

    $('#modalAgregar').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    $('#modalExito').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    $('#modalEliminar').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    $('#modalEditar').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

    $('.modal').on('hidden.bs.modal', function () {
        clearBackdrop();
    });

});
