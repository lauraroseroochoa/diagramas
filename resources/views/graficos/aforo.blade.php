<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Highcharts Exporting Module</title>
    <script src="https://code.highcharts.com/11.4.7/highcharts.js"></script>
    <script src="https://code.highcharts.com/11.4.7/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/11.4.7/modules/export-data.js"></script>
</head>
<body>
    <div id="container"></div>

    <script>
        // Example chart with exporting options
        Highcharts.chart('container', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Sample Data'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
                title: {
                    text: 'Values'
                }
            },
            series: [{
                name: 'Example',
                data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
            }],
            exporting: {
                enabled: true,
                showTable: true
            }
        });
    </script>
</body>
</html>
