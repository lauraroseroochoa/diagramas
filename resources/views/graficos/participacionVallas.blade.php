<div class="col  ms-2  " style="height:65vh;">
    
<h5 class="text-dos pt-2" >Participación por empresa con respecto a toda la pauta $comercial vendida</h5>
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
        text: '',
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
                
                    {
                        name: '{{$empresas[$key]->descripcion}}',
                        y: {{ number_format(($value['ventas'] * 100 / ($totalPautaComercial)), 2) }},
                        @if($empresas[$key]->descripcion == 'Marketmedios') color: '#FF0000' @endif
                    },
                
            @endforeach
            ],
        colorByPoint: true,
        colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
    }]
});
</script>