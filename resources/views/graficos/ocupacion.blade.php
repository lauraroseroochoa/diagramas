<style>
	/* Estilo para alinear los gráficos en fila */
	.charts-container {
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap; /* Permite que los gráficos se ajusten si hay demasiado espacio */
	}
	.chart {
		width: 30%; /* Ajusta el ancho de los gráficos según sea necesario */
		min-width: 300px; /* Tamaño mínimo del gráfico */
		margin: 5px; /* Espaciado entre los gráficos */
	}
</style>

<figure class="highcharts-figure">
	<div id="container1"  style="width: 100%; height: 500px;"></div>
	<div class="charts-container">
        <div id="container2" class="chart"></div>
        <div id="container3" class="chart"></div>
        <div id="container4" class="chart"></div>
    </div>
</figure>
</div>
<script>
    var propietarios = @json($propietarioNombre);
    var porcentajeVallas = @json($porcentajes);
	var propietarios2 = @json($propietarioNombre2);
    var porcentajeVallas2 = @json($porcentajes2);
	var propietarios3 = @json($propietarioNombre3);
    var porcentajeVallas3 = @json($porcentajes3);
    var total = @json($total);
    console.log(propietarios);
    console.log(porcentajeVallas);
	console.log(total);
    Highcharts.chart('container1', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 5,
                    depth: 70
                }
            },
            title: {
                text: 'Participacion por empresa',
                align: 'left'
            },
			subtitle: {
                text: 'Respecto a la pauta total vendida',
                align: 'left'
            },
            
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                type: 'category',
				categories: propietarios,
                labels: {
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: '% Caras Vendidas',
                    margin: 20
                }
            },
            tooltip: {
                valueSuffix: '%'
            },
            series: [{
                name: 'Vallas',
                data: porcentajeVallas 
            }]
        });
		Highcharts.chart('container2', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 5,
                    depth: 70
                }
            },
            title: {
                text: 'Ocupación LED',
                align: 'left'
            },
            
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                type: 'category',
				categories: propietarios2,
                labels: {
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Porcentaje',
                    margin: 20
                }
            },
            tooltip: {
                valueSuffix: '%'
            },
            series: [{
                name: 'LED',
                data: porcentajeVallas2 
            }]
        });
		Highcharts.chart('container3', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 5,
                    depth: 70
                }
            },
            title: {
                text: 'Ocupación Tradicional',
                align: 'left'
            },
            
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                type: 'category',
				categories: propietarios3,
                labels: {
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Porcentaje',
                    margin: 20
                }
            },
            tooltip: {
                valueSuffix: '%'
            },
            series: [{
                name: 'Tradicional',
                data: porcentajeVallas3 
            }]
        });
		Highcharts.chart('container4', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 5,
                    depth: 70
                }
            },
            title: {
                text: 'Ocupación General',
                align: 'left'
            },
            
            plotOptions: {
                column: {
                    depth: 25
                }
            },
            xAxis: {
                type: 'category',
				categories: propietarios,
                labels: {
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Porcentaje',
                    margin: 20
                }
            },
            tooltip: {
                valueSuffix: '%'
            },
            series: [{
                name: 'General',
                data: porcentajeVallas 
            }]
        });
</script>
