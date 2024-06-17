document.addEventListener('DOMContentLoaded', function() {
    let ingresosMesChart;
    let dispositivosPorEstablecimientoChart;

    // Obtener y mostrar las cantidades reales de los dispositivos
    $.ajax({
        url: '../controllers/dispGrafics_controlador.php',
        method: 'GET',
        success: function(response) {
            let data;
            if (typeof response === 'string') {
                try {
                    data = JSON.parse(response);
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    return;
                }
            } else {
                data = response;
            }

            $('#enEspera-count').text(data.estadoDispositivos.enEspera || 0);
            $('#reparados-count').text(data.estadoDispositivos.reparados || 0);
            $('#noReparados-count').text(data.estadoDispositivos.noReparados || 0);

            // Destruir el gráfico existente si existe
            if (ingresosMesChart) {
                ingresosMesChart.destroy();
            }

            // Crear un nuevo gráfico para ingresos del mes
            const ctxIngresos = document.getElementById('ingresosMesChart').getContext('2d');
            ingresosMesChart = new Chart(ctxIngresos, {
                type: 'bar',
                data: {
                    labels: Object.keys(data.ingresosDelMes),
                    datasets: [{
                        label: 'Ingresos del Mes',
                        data: Object.values(data.ingresosDelMes),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        }
                    }
                }
            });

            // Destruir el gráfico existente si existe
            if (dispositivosPorEstablecimientoChart) {
                dispositivosPorEstablecimientoChart.destroy();
            }

            // Crear un nuevo gráfico para dispositivos por establecimiento
            const ctxEstablecimiento = document.getElementById('establishmentChart').getContext('2d');
            dispositivosPorEstablecimientoChart = new Chart(ctxEstablecimiento, {
                type: 'bar',
                data: {
                    labels: Object.keys(data.dispositivosPorEstablecimiento),
                    datasets: [{
                        label: 'Dispositivos por Establecimiento',
                        data: Object.values(data.dispositivosPorEstablecimiento),
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#000', // Color de las etiquetas del eje X
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000', // Color de las etiquetas del eje Y
                            }
                        }
                    }
                }
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
        }
    });
});
