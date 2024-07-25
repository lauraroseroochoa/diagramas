<!DOCTYPE html>
<html>
<head>
    <title>Productos Filtrados</title>
    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        #container, #containerVallas, #containerTransmilenio {
            height: 400px;
            margin-bottom: 1em;
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
</head>
<body>
    <figure class="highcharts-figure">
        <div id="container"></div>
    </figure>
    <figure class="highcharts-figure">
        <div id="containerVallas"></div>
    </figure>
    <figure class="highcharts-figure">
        <div id="containerTransmilenio"></div>
    </figure>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productos = @json($productosSeleccionados->pluck('producto'));
            const porcentajeTipolugar1List = @json($porcentajesTipolugar1List);
            const porcentajeTipolugar3List = @json($porcentajesTipolugar3List);

            const porcentajeTotalList = productos.map((producto, index) => {
                return porcentajeTipolugar1List[index] + porcentajeTipolugar3List[index];
            });

            const categoriasPersonalizadas = productos.map((producto, index) => {
                return `${producto} (${porcentajeTotalList[index]}%)`;
            });

            // Gráfico de columnas
            Highcharts.chart('container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Porcentajes de Productos por Tipo de Lugar'
                },
                xAxis: {
                    categories: categoriasPersonalizadas,
                    title: {
                        text: 'Productos'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Porcentaje'
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [
                    {
                        name: 'Vallas',
                        data: porcentajeTipolugar1List,
                        color: '#EBC0E5'
                    },
                    {
                        name: 'Transmilenio',
                        data: porcentajeTipolugar3List,
                        color: '#B9F4DC'
                    }
                ]
            });

            // Gráfico de torta para Vallas
            Highcharts.chart('containerVallas', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Porcentajes de Vallas por Producto'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            connectorColor: 'silver'
                        }
                    }
                },
                series: [{
                    name: 'Vallas',
                    data: productos.map((producto, index) => {
                        return { name: producto, y: porcentajeTipolugar1List[index] };
                    })
                }]
            });

            // Gráfico de torta para Transmilenio
            Highcharts.chart('containerTransmilenio', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Porcentajes de Transmilenio por Producto'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            connectorColor: 'silver'
                        }
                    }
                },
                series: [{
                    name: 'Transmilenio',
                    data: productos.map((producto, index) => {
                        return { name: producto, y: porcentajeTipolugar3List[index] };
                    })
                }]
            });
        });
    </script>
</body>
</html>

