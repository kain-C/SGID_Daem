$(document).ready(function() {
    function sendNotification(title, options) {
        if (Notification.permission === 'granted') {
            new Notification(title, options);
        }
    }

    if (Notification.permission !== 'granted') {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Permiso de notificación concedido.');
            }
        });
    }

    var usuario_id = $('#usuario_id').val();

    function cargarRecordatorios() {
        return $.ajax({
            url: '../controllers/RecordatorioController.php?action=cargar',
            method: 'GET',
            dataType: 'json'
        });
    }

    function cargarEventos() {
        return $.ajax({
            url: '../controllers/calendarDcontroller.php',
            method: 'GET',
            dataType: 'json'
        });
    }

    function cargarEventosCalendario() {
        $('#loadingSpinner').show();
        $.when(
            cargarEventos(),
            cargarRecordatorios()
        ).then(function(dispositivosData, recordatoriosData) {
            try {
                console.log("Datos de dispositivos recibidos del servidor: ", dispositivosData[0]);
                console.log("Datos de recordatorios recibidos del servidor: ", recordatoriosData[0]);

                var eventColors = {
                    'Actualización de Software': '#ffd380',
                    'Reparación': '#ffa600',
                    'Mantenimiento Preventivo': '#ff8531',
                    'Sustitución de Componentes': '#bc5090',
                    'Inspección Técnica': '#8a508f',
                    'Calibración': '#8a508f',
                    'Limpieza y Mantenimiento General': '#2c4875',
                    'Diagnóstico': '#003f5c',
                    'Revisión de Seguridad': '#003f5c',
                    'Reparación de Emergencia': '#00202e',
                };

                var events = dispositivosData[0].map(function(device) {
                    var startDate = new Date(device.FechaRegistro);
                    if (isNaN(startDate)) {
                        throw new RangeError("Invalid date in dispositivosData: " + device.FechaRegistro);
                    }
                    var endDate = new Date(device.FechaRegistro);
                    endDate.setDate(endDate.getDate() + 3);
                    endDate.setHours(18, 0, 0); // Establecer la hora de finalización a las 18:00

                    var eventColor = eventColors[device.accion] || '#000000';

                    return {
                        title: device.Tipo_dispositivo + ' (' + device.accion + ')',
                        start: startDate.toISOString(),
                        end: endDate.toISOString(),
                        description: 'ID Dispositivo: ' + device.ID_Dispositivo + '\nModelo: ' + device.Modelo + '\nAcción: ' + device.accion,
                        color: eventColor,
                        extendedProps: {
                            accion: device.accion,
                            id: device.ID_Dispositivo
                        }
                    };
                });

                events = events.concat(recordatoriosData[0].map(function(recordatorio) {
                    var startDate = new Date(recordatorio.fecha);
                    if (isNaN(startDate)) {
                        throw new RangeError("Invalid date in recordatoriosData: " + recordatorio.fecha);
                    }

                    return {
                        title: recordatorio.titulo,
                        start: startDate.toISOString(),
                        end: startDate.toISOString(), // Mantener la misma fecha y hora de fin que la de inicio
                        description: recordatorio.descripcion,
                        color: '#ff9f89',
                        extendedProps: {
                            accion: 'Recordatorio',
                            id: recordatorio.id,
                            usuario_id: recordatorio.usuario_id,
                            usuario_nombre: recordatorio.usuario_nombre
                        }
                    };
                }));

                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listWeek,listDay'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                        listWeek: 'Agenda Semanal',
                        listDay: 'Agenda Diaria'
                    },
                    views: {
                        listWeek: {
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true // Formato AM/PM
                            }
                        },
                        listDay: {
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true // Formato AM/PM
                            }
                        },
                        dayGridMonth: {
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true, // Formato AM/PM
                                omitZeroMinute: true
                            }
                        },
                        timeGridWeek: {
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true, // Formato AM/PM
                                omitZeroMinute: true
                            }
                        },
                        timeGridDay: {
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true, // Formato AM/PM
                                omitZeroMinute: true
                            }
                        }
                    },
                    events: events,
                    editable: true,
                    eventDrop: function(info) {
                        if (info.event.extendedProps.accion === 'Recordatorio' && info.event.extendedProps.usuario_id == usuario_id) {
                            var newDate = info.event.start.toISOString().slice(0, 19).replace('T', ' ');
                            $.ajax({
                                url: '../controllers/RecordatorioController.php?action=modificar',
                                method: 'POST',
                                data: {
                                    id: info.event.extendedProps.id,
                                    titulo: info.event.title,
                                    descripcion: info.event.extendedProps.description,
                                    fecha: newDate
                                },
                                success: function(response) {
                                    alert(response);
                                    cargarEventosCalendario();
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error en la solicitud AJAX: ", status, error);
                                    info.revert();
                                }
                            });
                        } else {
                            info.revert();
                        }
                    },
                    eventClick: function(info) {
                        if (info.event.extendedProps.accion === 'Recordatorio') {
                            if (info.event.extendedProps.usuario_id == usuario_id) {
                                $('#recordatorio_id_modificar').val(info.event.extendedProps.id);
                                $('#titulo_modificar').val(info.event.title);
                                $('#descripcion_modificar').val(info.event.extendedProps.description);
                                $('#fecha_modificar').val(moment(info.event.start).format('YYYY-MM-DD'));
                                $('#hora_modificar').val(moment(info.event.start).format('HH:mm'));
                                $('#usuario_nombre_modificar').val(info.event.extendedProps.usuario_nombre);
                                $('#modificarRecordatorioModalLabel').text('Modificar Recordatorio');
                                $('#eliminarRecordatorio').show();
                                $('#modificarRecordatorioModal').modal('show');
                            } else {
                                $('#visualizarUsuarioNombre').text(info.event.extendedProps.usuario_nombre);
                                $('#visualizarTitulo').text(info.event.title);
                                $('#visualizarDescripcion').text(info.event.extendedProps.description);
                                $('#visualizarFecha').text(moment(info.event.start).format('YYYY-MM-DD HH:mm'));
                                $('#visualizarRecordatorioModal').modal('show');
                            }
                        } else {
                            $('#modal-event-title').text(info.event.title);
                            $('#modal-event-description').text(info.event.extendedProps.description);
                            $('#eventModal').modal('show');
                        }
                    }
                });

                calendar.render();
            } catch (error) {
                console.error("Error al procesar datos: ", error);
            } finally {
                $('#loadingSpinner').hide();
            }
        }).fail(function() {
            console.error("Error al cargar datos del servidor.");
            $('#loadingSpinner').hide();
        });
    }

    cargarEventosCalendario();

    $('#agregarRecordatorioForm').on('submit', function(e) {
        e.preventDefault();

        var fecha = $('#fecha').val();
        var hora = $('#hora').val();
        var fechaHora = fecha + ' ' + hora;

        $.ajax({
            url: '../controllers/RecordatorioController.php?action=guardar',
            method: 'POST',
            data: $(this).serialize() + '&fecha=' + fechaHora,
            success: function(response) {
                alert(response);
                $('#agregarRecordatorioForm')[0].reset();
                $('#agregarRecordatorioModal').modal('hide');
                cargarEventosCalendario();
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
            }
        });
    });

    $('#modificarRecordatorioForm').on('submit', function(e) {
        e.preventDefault(); // Aquí corregimos el error tipográfico
        console.log('Formulario de modificación enviado');

        var fecha = $('#fecha_modificar').val();
        var hora = $('#hora_modificar').val();
        var fechaHora = fecha + ' ' + hora;

        var id = $('#recordatorio_id_modificar').val();
        var action = 'modificar';

        $.ajax({
            url: '../controllers/RecordatorioController.php?action=' + action,
            method: 'POST',
            data: $(this).serialize() + '&fecha=' + fechaHora,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                alert(response);
                $('#modificarRecordatorioForm')[0].reset();
                $('#recordatorio_id_modificar').val('');
                $('#eliminarRecordatorio').hide();
                $('#modificarRecordatorioModal').modal('hide');
                cargarEventosCalendario();
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
            }
        });
    });

    $('#eliminarRecordatorio').on('click', function() {
        var id = $('#recordatorio_id_modificar').val();

        if (confirm('¿Estás seguro de que quieres eliminar este recordatorio?')) {
            $.ajax({
                url: '../controllers/RecordatorioController.php?action=eliminar',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    alert(response);
                    $('#modificarRecordatorioForm')[0].reset();
                    $('#recordatorio_id_modificar').val('');
                    $('#eliminarRecordatorio').hide();
                    $('#modificarRecordatorioModal').modal('hide');
                    cargarEventosCalendario();
                },
                error: function(xhr, status, error) {
                    console.error("Error en la solicitud AJAX: ", status, error);
                }
            });
        }
    });

    $('#btnAgregarRecordatorio').on('click', function() {
        $('#agregarRecordatorioForm')[0].reset();
        $('#fecha').val(moment().format('YYYY-MM-DD')); // Fecha actual por defecto
        $('#hora').val(''); // Limpiar el campo de hora
        $('#agregarRecordatorioModalLabel').text('Agregar Recordatorio');
        $('#agregarRecordatorioModal').modal('show');
    });
});
