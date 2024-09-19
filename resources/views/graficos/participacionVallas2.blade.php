<div class="col border ms-2  " style="height:65vh;">
<div id="participacion" style="width: 100%; height: 60vh;">
    
</div>
</div>
<script type="text/javascript">
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'participacion',
        type: 'column',
        options3d: {
            enabled: true,
            alpha: 0,
            beta: 0,
            depth: 28,
            viewDistance: 25
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            enabled: true,
            text: 'cantidad de pantallas'
        },

    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: 'Pantallas: {point.y}'
    },
    title: {
        text: 'Participación por empresa propietaria de pantallas LED con respecto a toda la pauta comercial vendida',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        column: {
            depth: 25
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
                @foreach($datos2 as $key => $value)
                ['{{$empresas[$key]->descripcion}}', {{ number_format(($value['ventas']*100/($totalPautaComercial)), 2) }}],
                @endforeach
            ],
        colorByPoint: true,
        colors: [
                @foreach($datos2 as $key => $value)
                '#{{$empresas[$key]->color}}',
                @endforeach
            ] 
    }]
});
</script>