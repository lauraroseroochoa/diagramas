<div class="row mb-2 mt-0 pt-0" style=" ">
    <div class="col-1 border ms-3 p-2 pt-0 d-flex flex-column bg-white" style="height: 25vh; max-height: 25vh;" >
        <h1 class="text-uno flex-grow-1 d-flex align-items-center counter" style="font-size:65px;">{{ $totalLugares }}</h1>
        <div class="border-bottom border-uno border-3"> Total {{ $varTotalLugares }}</div>
    </div>
    <div class="col border ms-3 p-0 " >
        <div class id="container2" class="d-flex flex-wrap justify-content-between " style="height: 25vh; max-height:25vh; "> </div>
    </div>
</div>
<div class="row ps-3" style="height: 40vh; max-height: 40vh; ">
    <div class="col-6 border" >
        <div class="mt-2 " style="margin-bottom: -32px;"><h4 class="text-danger" >Pantallas LED activas por comercializador y lugar</h4></div>
        <table class="w-100  datatable1  " >
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Lugar</th>
                    <th>Pantallas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultadosPL as $key => $value)
                    @foreach($value as $key2 => $value2)
                        <tr>
                            <td>
                                @php
                                    $propietarios2 = explode(",",$key2);
                                @endphp
                                @foreach($propietarios2 as $k => $v)
                                    {{ isset($propietarios[$v])?$propietarios[$v]->descripcion:'' }} - 
                                @endforeach

                            </td>
                            <td>{{ isset($lugares[$key])?$lugares[$key]:'' }}</td>
                            <td>{{ $value2 }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totales</th>
                    <th class="ps-4">Pantallas</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-6 border" >
        <div class="mt-2"  style="margin-bottom: -32px;"><h4 class="text-danger" >Pantallas LED activas por lugar</h4></div>
        <table class="w-100  datatable2  " >
            <thead>
                <tr>
                    <th>Lugar</th>
                    <th>Pantallas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultadosL as $key => $value)
                    <tr>
                        <td>{{ isset($lugares[$key])?$lugares[$key]:'' }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th >Totales</th>
                    <th class="ps-4">Pantallas</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">
    $('.datatable1 thead tr:eq(0) th').each( function (i) {
        $(this).append( '<input type="text" placeholder="Buscar: " style="width:100%; display:block; font-size:11px; line-height:15px; margin:1px 0px 0px 0px; border:none; border:1px solid #f2f2f2;" />'  );
        $( 'input', this ).on( 'keyup change', function () {
          if ( table1.column(i).search() !== this.value ) {
              table1
                  .column(i)
                  .search( this.value )
                  .draw();
          }
        } );
    });

    $('.datatable2 thead tr:eq(0) th').each( function (i) {
        $(this).append( '<input type="text" placeholder="Buscar: " style="width:100%; display:block; font-size:11px; line-height:15px; margin:1px 0px 0px 0px; border:none; border:1px solid #f2f2f2;" />'  );
        $( 'input', this ).on( 'keyup change', function () {
          if ( table2.column(i).search() !== this.value ) {
              table2
                  .column(i)
                  .search( this.value )
                  .draw();
          }
        } );
    });

    var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[0, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "22vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            // Calcula el total de cada columna
            var total = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);
            // Muestra los totales en el pie de página
            $(api.column(2).footer()).html(total);
        }
    });


    var table2 = $('.datatable2').DataTable({
        paging: false,
        order: [[1, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "22vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            // Calcula el total de cada columna
            var total = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);
            // Muestra los totales en el pie de página
            $(api.column(1).footer()).html(total);
        }
    });


    var resultadosP=@json($resultadosP);

    // Convertir el objeto en un array de objetos con 'name' y 'y'
    var formattedData = Object.keys(resultadosP).map(function(key) {
        return {
            name: key,
            y: resultadosP[key]
        };
    });

    // Set up the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container2',
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
                text: 'cantidad'
            },

        },
        tooltip: {
            headerFormat: '<b>{point.key}</b><br>',
            pointFormat: 'Pantallas: {point.y}'
        },
        title: {
            text: 'Pantallas LED activas por propietario',
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
                    format: '{point.y}' // Muestra el valor en la columna
                }
            }
        },
        credits: {
           enabled: false
        },
        series: [{
            data: [
                @foreach($resultadosP as $key => $value)
                ['{{$propietarios[$key]->descripcion}}', {{ $value }}],
                @endforeach
            ],
            colorByPoint: true,
            colors: [
                @foreach($resultadosP as $key => $value)
                '#{{ ($propietarios[$key]->color==''?'ddd':$propietarios[$key]->color) }}',
                @endforeach
            ] 
        }]
    });

    


</script>
