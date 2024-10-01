<div class="row m-0 p-0">
    <div class="col p-0 m-0 ">
        <h5 class="text-dos pt-2" style="margin-bottom: -27px;">Empresas por cupos vendidos</h5>
        <table id="cantidad" class="datatable1 table table-striped tableFont w-100 p-0 m-0 ">
            <thead>
                <tr>
                    <th class="border">Empresa</th>
                    <th class="border">Tradicional</th>
                    <th class="border">Led</th>
                    <th class="border">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ocupacionGeneral as $key => $value)
                <tr>
                    @php
                        $clase = '';
                        if($key==2){
                            $clase = 'text-danger';
                        }
                    @endphp
                    <td class="border {{$clase}}">{{ strtoupper($empresas[$key]->descripcion) }}</td>
                    <td class="border" data-order="{{ isset($ocupacionTradicional[$key]['ventas']) ? $ocupacionTradicional[$key]['ventas'] : 0 }}">
                        {{ isset($ocupacionTradicional[$key]['ventas'])?$ocupacionTradicional[$key]['ventas']:0; }}
                        <span class="float-end p-1 " style="width:64px; font-size:11px; border:1px solid #999;">{{ isset($ocupacionTradicional[$key]['ventas'])?$ocupacionTradicional[$key]['ventas'].' de '.$ocupacionTradicional[$key]['total']:'0 de 0'}}</span>
                    </td>
                    <td class="border" data-order="{{ isset($ocupacionLed[$key]['ventas']) ? $ocupacionLed[$key]['ventas'] : 0 }}">
                        {{ isset($ocupacionLed[$key]['ventas'])?$ocupacionLed[$key]['ventas']:0;}}
                        <span class="float-end p-1 " style="width:64px; font-size:11px; border:1px solid #999;">{{ isset($ocupacionLed[$key]['ventas'])?$ocupacionLed[$key]['ventas'].' de '.$ocupacionLed[$key]['total']:'0 de 0'}}</span>
                    </td>
                    <td class="border" data-order="{{ isset($ocupacionGeneral[$key]['ventas']) ? $ocupacionGeneral[$key]['ventas'] : 0 }}">
                        {{ isset($ocupacionGeneral[$key]['ventas'])?$ocupacionGeneral[$key]['ventas']:0;}}
                        <span class="float-end p-1 " style="width:64px; font-size:11px; border:1px solid #999;">{{ isset($ocupacionGeneral[$key]['ventas'])?$ocupacionGeneral[$key]['ventas'].' de '.$ocupacionGeneral[$key]['total']:'0 de 0'}}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Totales</th>
                    <th>Tradicional</th>
                    <th>Led</th>
                    <th>Total</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-8  m-0 p-0 ms-2">
        <div class="row m-0 p-0">
            <div class="col-4 border bg-white">
                <h5 class="text-dos pt-2">Ocupación led (Base cupos vendidos)</h5>
                <div id="ocupacionLed" style=" height: 35vh;"></div>
            </div>
            <div class="col-4 ms-2 border bg-white" >
                <h5 class="text-dos pt-2">Ocupación tradicional</h5>
                <div id="ocupacionTradicional" style=" height: 35vh;"></div>
            </div>
        
            <div class="col border bg-white">
                <h5 class="text-dos pt-2">Ocupación general</h5>
                <div id="ocupacionGeneral"  style=" height: 38vh;"></div>
            </div>
        </div>
        <div class="row m-0 p-0 mt-2">
            <div class="col m-0 p-0">
                <h5 class="text-dos pt-2">Participación (Cupos vendidos vs total cupos vendidos)</h5>
                <div id="participacion"  style=" height: 35vh;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var chart1 = new Highcharts.Chart({
    chart: {
        renderTo: 'ocupacionLed',
        type: 'bar',
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
            enabled: false,
            text: '% ocupacion'
        },
    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: 'Ocupacion: {point.y}'
    },
    title: {
        text: '',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        bar: {
            depth: 25,
            dataLabels: {
                enabled: true,
                format: '{point.y} %' // Muestra el valor en la bara
            }
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
            @foreach($ocupacionLed as $key => $value)
                @if($value['ventas'] > 0)
                    {
                        name: '{{$empresas[$key]->descripcion}}',
                        y: {{ number_format(($value['ventas'] * 100 / ($value['total'])), 2) }},
                        @if($empresas[$key]->descripcion == 'Marketmedios') color: '#FF0000' @endif
                    },
                @endif
            @endforeach
        ],
        colorByPoint: true,
        colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
    }]
});

var chart2 = new Highcharts.Chart({
    chart: {
        renderTo: 'ocupacionTradicional',
        type: 'bar',
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
            enabled: false,
            text: 'Cantidad de pantallas'
        },
    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: 'Ocupacion: {point.y}'
    },
    title: {
        text: '',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        bar: {
            depth: 25,
            dataLabels: {
                enabled: true,
                format: '{point.y} %' // Muestra el valor en la bara
            }
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
            
            @foreach($ocupacionTradicional as $key => $value)
                @if($value['ventas'] > 0)
                    {
                        name: '{{$empresas[$key]->descripcion}}',
                        y: {{ number_format(($value['ventas'] * 100 / ($value['total'])), 2) }},
                        @if($empresas[$key]->descripcion == 'Marketmedios') color: '#FF0000' @endif
                    },
                @endif
            @endforeach
        ],
        colorByPoint: true,
        colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
    }]
});

var chart3 = new Highcharts.Chart({
    chart: {
        renderTo: 'ocupacionGeneral',
        type: 'bar',
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
            enabled: false,
            text: 'Cantidad de pantallas'
        },
    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br>',
        pointFormat: 'Ocupacion: {point.y}'
    },
    title: {
        text: '',
        align: 'left'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        bar: {
            depth: 25,
            dataLabels: {
                enabled: true,
                format: '{point.y} %' // Muestra el valor en la bara
            }
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
            @foreach($ocupacionGeneral as $key => $value)
                @if($value['ventas'] > 0)
                    {
                        name: '{{$empresas[$key]->descripcion}}',
                        y: {{ number_format(($value['ventas'] * 100 / ($value['total'])), 2) }},
                        @if($empresas[$key]->descripcion == 'Marketmedios') color: '#FF0000' @endif
                    },
                @endif
            @endforeach
        ],
        colorByPoint: true,
        colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
    }]
});


var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[3, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "60vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Calcula el total de cada columna
            var totalEdad = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

            var totalPosicion = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

            var totalOficina = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);

            // Muestra los totales en el pie de página
            $(api.column(1).footer()).html(totalEdad);
            $(api.column(2).footer()).html(totalPosicion);
            $(api.column(3).footer()).html(totalOficina);
        }
    });

var chart4 = new Highcharts.Chart({
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
            depth: 25,
            dataLabels: {
                enabled: true,
                format: '{point.y} %' // Muestra el valor en la bara
            }
        }
    },
    credits: {
       enabled: false
    },
    series: [{
        data: [
                @foreach($participacion as $key => $value)
                    @if(isset($participacion[$key]['ventas']) && $totalPautaComercial>0 )
                    {
                        name: '{{$empresas[$key]->descripcion}}',
                        y: {{ number_format(($value['ventas'] * 100 / ($totalPautaComercial)), 2) }},
                        @if($empresas[$key]->descripcion == 'Marketmedios') color: '#FF0000' @endif
                    },
                    @endif
            @endforeach
            ],
        colorByPoint: true,
        colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
    }]
});

</script>