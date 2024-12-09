
<div class="row m-0 p-0">

    <div class="col p-0 m-0 ">
        <h5 class="text-dos pt-2" style="margin-bottom: 0px;">Empresas por cupos vendidos</h5>
        <table id="cantidad" class="datatable1 table table-striped tableFont w-100 p-0 m-0 bg-white ">
            <thead>
                <tr>
                    <th class="border">Empresa</th>
                    <th class="border">Cupos vendidos</th>
                    <th class="border">Total cupos LED</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos2 as $key => $value)
                    <tr>
                        <td>{{$empresas[$key]->descripcion}}</td>
                        <td>{{ $value['ventas'] }}</td>
                        <td>{{ $value['total']*6 }}</td>
                    </tr>
                @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <th>Totales</th>
                    <th>Cupos vendidos</th>
                    <th>Total cupos LED</th>
                </tr>
            </tfoot>

        </table>
    </div>
    <div class="col-8 border ms-2  " style="height:65vh;">
    <div id="ocupacion" style="width: 100%; height: 60vh;">

    </div>
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
                ['{{$empresas[$key]->descripcion}}', {{ number_format(($value['ventas']*100/($value['total']*6)), 2) }}],
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

var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[2, 'desc']],
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


            // Muestra los totales en el pie de página
            $(api.column(1).footer()).html(totalEdad);
            $(api.column(2).footer()).html(totalPosicion);
        }
    });
</script>