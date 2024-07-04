<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos para Producto</title>
    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px; 
            max-width: 800px;
            margin: 1em auto;
        }

        #container1, #container2 {
            height: 400px;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
</head>
<body>
    <h1>Gráfico para {{ $producto->producto }} 2024</h1>

    <figure class="highcharts-figure">
        <div id="container1"></div>
        <p class="highcharts-description">
            Gráfico mostrando columnas apiladas Estudio competencia
        </p>
    </figure>

    <figure class="highcharts-figure">
        <div id="container2"></div>
        <p class="highcharts-description">
            Gráfico mostrando barras de columnas agrupadas Participacion
        </p>
    </figure>

    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    {{-- <script src="https://code.highcharts.com/highcharts-3d.js"></script> --}}
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const datosVallas = @json($datosVallas);
            const datosTransmilenio = @json($datosTransmilenio);
            const datosVallasPorcentaje = @json($datosVallasPorcentaje);
            const datosTransmilenioPorcentaje = @json($datosTransmilenioPorcentaje);
            const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            console.log('Datos Vallas:', datosVallas);
            console.log('Datos Transmilenio:', datosTransmilenio);
            console.log('Datos Vallas Porcentaje:', datosVallasPorcentaje);
            console.log('Datos Transmilenio Porcentaje:', datosTransmilenioPorcentaje);

            // Gráfico de columnas apiladas en 3D
            Highcharts.chart('container1', {
                chart: {
                    type: 'column',
                    options3d: {
                        enabled: true,
                        alpha: 5,
                        beta: 5,
                        viewDistance: 25,
                        depth: 40
                    }
                },
                title: {
                    text: 'Estudio de Competencia'
                },
                xAxis: {
                    categories: nombresMeses,
                    labels: {
                        skew3d: true,
                        style: {
                            fontSize: '16px'
                        }
                    }
                },
                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Total',
                        skew3d: true,
                        style: {
                            fontSize: '16px'
                        }
                    }
                },
                
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        depth: 40
                    }
                },
                series: [{
                    name: 'Vallas',
                    data: datosVallas
                }, {
                    name: 'Transmilenio',
                    data: datosTransmilenio
                }]
            });

            // Gráfico de columnas agrupadas
            Highcharts.chart('container2', {
                chart: {
                    type: 'column',
                    options3d: {
                        enabled: true,
                        alpha: 5,
                        beta: 5,
                        depth: 30,
                        viewDistance: 35
                    }
                },
                title: {
                    text: 'Participacion'
                },
                xAxis: {
                    categories: nombresMeses,
                    labels: {
                        skew3d: true,
                        style: {
                            fontSize: '16px'
                        }
                    }
                },
                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Total',
                        skew3d: true,
                        style: {
                            fontSize: '16px'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y}'
                },
                plotOptions: {
                    column: {
                        grouping: true,
                        depth: 25
                    }
                },
                series: [{
                    name: 'Vallas',
                    data: datosVallasPorcentaje
                }, {
                    name: 'Transmilenio',
                    data: datosTransmilenioPorcentaje
                }]
            });
        });
    </script>
</body>
</html>
