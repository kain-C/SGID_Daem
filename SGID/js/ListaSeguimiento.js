$(document).ready(function() {
    function renderizarTabla() {
        if ($.fn.DataTable.isDataTable('#tablaHistorialDispositivos')) {
            $('#tablaHistorialDispositivos').DataTable().clear().destroy();
        }

        $('#tablaHistorialDispositivos').DataTable({
            "ajax": {
                "url": "../controllers/DispositivoController.php?action=getDispositivos",
                "type": "GET",
                "dataSrc": function(response) {
                    if (!response.data) {
                        console.error('Error en la respuesta JSON:', response);
                        $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                        return [];
                    }
                    // Filtrar dispositivos duplicados por Número de Serie
                    const uniqueDevices = [];
                    const seenSerials = new Set();
                    response.data.forEach(device => {
                        if (!seenSerials.has(device.Num_Serie)) {
                            uniqueDevices.push(device);
                            seenSerials.add(device.Num_Serie);
                        }
                    });
                    return uniqueDevices;
                },
                "error": function(xhr, error, thrown) {
                    console.error('Error en la solicitud AJAX:', error);
                    $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                }
            },
            "columns": [
                {"data": "ID_Dispositivo"},
                {"data": "Tipo_dispositivo"},
                {"data": "Modelo"},
                {"data": "Num_Serie"},
                {"data": "NombreMarca"},
                {"data": "NombreEstablecimiento"},
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return `<button class="btn btn-info" onclick="verHistorial('${data.Num_Serie}')">Ver Historial</button>`;
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
        });
    }

    window.verHistorial = function(numSerie) {
        $.ajax({
            url: '../controllers/DispositivoController.php?action=obtenerDispositivosPorSerie',
            method: 'GET',
            data: { num_serie: numSerie },
            dataType: 'json',
            success: function(response) {
                console.log('Response data:', response.data); // Añadido para depuración
                if (response.data) {
                    $('#tablaHistorialMantenimiento').DataTable().clear().destroy(); // Limpiar y destruir la tabla anterior si existe
                    $('#tablaHistorialMantenimiento').DataTable({
                        "data": response.data,
                        "columns": [
                            {"data": "ID_Dispositivo"},
                            {"data": "FechaRegistro"},
                            {"data": "Descripcion"},
                            {
                                "data": "Estado",
                                "render": function(data, type, row, meta) {
                                    let estadoIcon = '';
                                    let estadoTooltip = '';
                                    if (data === 'Reparado') {
                                        estadoIcon = 'bi bi-check-circle estado-icon estado-icon-reparado';
                                        estadoTooltip = 'Reparado';
                                    } else if (data === 'No Reparado') {
                                        estadoIcon = 'bi bi-x-circle estado-icon estado-icon-no-reparado';
                                        estadoTooltip = 'No Reparado';
                                    }
                                    return `<i class="${estadoIcon}" data-toggle="tooltip" data-placement="top" title="${estadoTooltip}"></i>`;
                                }
                            }
                        ],
                        "paging": true,
                        "lengthMenu": [5, 10, 20],
                        "pageLength": 5,
                        "language": {
                            "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                        },
                        "dom": '<"top"fl>rt<"bottom"ip><"clear">',
                        "responsive": true,
                    });
                    $('#modalHistorial').modal('show');
                    $('[data-toggle="tooltip"]').tooltip(); // Inicializa tooltips
                } else {
                    console.error('Error al obtener el historial:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener el historial:', error);
            }
        });
    };

    renderizarTabla();
});