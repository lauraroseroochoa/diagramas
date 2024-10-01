

@foreach($datos2 as $key => $value)

    @if($key==2)
        {{ $value['ventas'] }}
        {{ $value['total'] }}
    @endif
@endforeach


<div class="col border ms-2  " style="height:65vh;">
<div id="ocupacion" style="width: 100%; height: 60vh;">
    
</div>
</div>
<script type="text/javascript">
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'ocupacion',
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
            text: 'Cantidad de pantallas'
        },
    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: 'Pantallas: {point.y}'
    },
    title: {
        text: 'Ocupación por empresa propietaria de pantallas LED con pauta comercial',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        column: {
            depth: 25,
            dataLabels: {
                enabled: true,
                format: '{point.y} %' // Muestra el valor en la columna
            }
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
            @foreach($datos2 as $key => $value)
                ['{{$empresas[$key]->descripcion}}', 
                @if($aplicarMultiplicacion)
                    {{ number_format(($value['ventas']*100/($value['total']*6)), 2) }}
                @else
                    {{ number_format($value['porcentaje'], 2) }},
                @endif
                ],
            @endforeach
        ],
        colorByPoint: true,
        colors: [
            @foreach($datos2 as $key => $value)
                '#{{ ($empresas[$key]->color == '' ? 'ddd' : $empresas[$key]->color) }}',
            @endforeach
        ]
    }]
});
</script>