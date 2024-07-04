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

        #container {
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productos = @json($productosSeleccionados->pluck('producto'));
            const porcentajeTipolugar1List = @json($porcentajesTipolugar1List);
            const porcentajeTipolugar3List = @json($porcentajesTipolugar3List);
            console.log(productos);
            console.log(porcentajeTipolugar1List);
            console.log(porcentajeTipolugar3List);
            Highcharts.chart('container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Porcentajes de Productos por Tipo de Lugar'
                },
                xAxis: {
                    categories: productos,
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
        });
    </script>
</body>
</html>