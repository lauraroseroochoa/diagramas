<div class="row m-0 p-0 mb-2">
    <div class="col border  p-2 bg-white" style="height: 12vh; max-height: 12vh; ">
        <h1 class="text-uno counter">{{ $totalLugaresActivos }}</h1>
        <div class="border-bottom border-uno border-3"> Caras Activas</div>
    </div>
    <div class="col border ms-2 p-2 bg-white" style="height: 12vh; max-height: 12vh; ">
        <h1 class="text-dos counter">{{ $totalCarasTradicional }}</h1>
        <div class="border-bottom border-dos border-3"> Tradicionales</div>
    </div>
    <div class="col border ms-2 p-2 bg-white" style="height: 12vh; max-height: 12vh; ">
        <h1 class="text-tres counter">{{ $totalCarasLed }}</h1>
       <div class="border-bottom border-tres border-3"> Led</div>
    </div>
    <div class="col border ms-2 p-2 bg-white" style="height: 12vh; max-height: 12vh; ">
        <h1 class="text-cuatro counter">{{ $totalDesactivados }}</h1>
        <div class="border-bottom border-cuatro border-3"> Desactivadas</div>
    </div>
    <div class="col border ms-2 p-2 bg-white" style="height: 12vh; max-height: 12vh; ">
        <h1 class="text-cinco counter">{{ $totalActivados }}</h1>
        <div class="border-bottom border-cinco  border-3"> Creadas</div>
    </div>
</div>
<div class="row m-0 p-0 ">
    <div class="col-6 border bg-white me-2" style="height: 55vh; max-height: 55vh; ">
        <div class="mt-3 " style="margin-bottom:-32px;">
            <h5 class="text-dos pt-1" >Caras creadas en el periodo</h5>
        </div>
        <table class="w-100  datatable1 tableaaaa " >
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Direccion</th>
                    <th>Tipo vallas</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activadoNuevo as $key => $value)
                @php
                    $clase = '';
                    if($value->propietarios_id==2){
                        $clase = 'text-danger';
                    }
                @endphp
                <tr>
                    <td class="{{$clase}}">{{ strtoupper(isset($propietarios[$value->propietarios_id])?$propietarios[$value->propietarios_id]->descripcion:'') }}</th>
                    <td>{{ strtoupper($value->direccion) }}</td>
                    <td > {{ strtoupper($tipovallas[$value->tipovalla_id]) }}</td>
                    <td >{{ $value->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col border bg-white">
        <div class="mt-3 " style="margin-bottom:-32px;">
            <h5 class="text-dos pt-1" >Caras desactivadas en el periodo</h5>
        </div>
        <table class="w-100  datatable2 tableaaaaa mtn-2 "  >
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Direccion</th>
                    <th>Tipo vallas</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($desactivados as $key => $value)
                <tr>
                    <td>{{ strtoupper(isset($propietarios[$value->propietarios_id])?$propietarios[$value->propietarios_id]->descripcion:'') }}</td>
                    <td>{{ strtoupper($value->direccion) }}</td>
                    <td> {{ strtoupper($tipovallas[$value->tipovalla_id]) }}</td>
                    <td>{{ $value->fecha_desactivacion }}</td>
                </tr>
            @endforeach
            </tbody>
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
        order: [[0, 'asc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "35vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ]
    });

    var table2 = $('.datatable2').DataTable({
        paging: false,
        order: [[0, 'asc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "35vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ]

    });
</script>
