$(document).ready(function() {
    function renderizarTablaHistorial() {
        if ($.fn.DataTable.isDataTable('#tablaHistorial')) {
            $('#tablaHistorial').DataTable().clear().destroy();
        }

        $('#tablaHistorial').DataTable({
            ajax: {
                url: "../controllers/HistorialController.php",
                type: "GET",
                data: function(d) {
                    d.accion = $('#filtroHistorial').val();
                },
                dataSrc: function(response) {
                    console.log('Respuesta de historial:', response);
                    if (!response.data) {
                        console.error('Error en la respuesta JSON:', response);
                        $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                        return [];
                    }
                    return response.data;
                },
                error: function(xhr, error, thrown) {
                    console.error('Error en la solicitud AJAX:', error);
                    $('#alertaError').text('Error al cargar los datos. Por favor, inténtalo de nuevo.').show();
                }
            },
            columns: [
                { data: "ID_Historial" },
                { data: "ID_Usuario" },
                { data: "FechaHora" },
                { data: "Accion" }
            ],
            paging: true,
            lengthMenu: [5, 10, 20, 50],
            pageLength: 5,
            order: [[2, 'desc']],  // Ordenar por la columna de fecha en orden descendente
            searching: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es_es.json",
                info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                lengthMenu: "Mostrar _MENU_ registros por página",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            dom: '<"top"fl>rt<"bottom"ip><"clear">',
            responsive: true,
            ordering: true  // Activar la ordenación
        });
    }

    $('#filtroHistorial').change(function() {
        renderizarTablaHistorial();
    });

    renderizarTablaHistorial();
});