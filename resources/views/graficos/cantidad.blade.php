<div class="row">
	<div class="col-7" style="height: 65vh; max-height: 65vh;  ">
		<table id="cantidad" class="datatable1 table table-striped tableFont w-100 ">
			<thead>
				<tr>
					<th class="border">Empresa</th>
					<th class="border">Tradicional</th>
					<th class="border">Led</th>
					<th class="border">Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($datos as $key => $value)
				<tr>
					<td class="border">{{ $key }}</td>
					<td class="border">{{ $value['tradicional'] }}</td>
					<td class="border">{{ $value['led'] }}</td>
					<td class="border">{{ $value['total'] }}</td>
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
	<div class="col-5 border container-fluid" id="openMap">

	</div>
</div>
 <style>
        .dataTables_wrapper {
            position: relative;
            height: 65vh; /* Ajusta la altura según tus necesidades */
            overflow: hidden;
        }

        .dataTables_scroll {
            height: 100%;
            overflow: hidden;
        }

        .dataTables_scrollBody {
            overflow-y: auto;
            height: calc(100% - 40px); /* Ajusta según la altura del header y footer */
        }

        .dataTables_scrollHead,
        .dataTables_scrollFoot {
            position: sticky;
            background: white; /* Asegura que el fondo sea blanco */
            z-index: 2;
        }

        .dataTables_scrollHead {
            top: 0;
        }

        .dataTables_scrollFoot {
            bottom: 0;
        }

        table.dataTable {
            border-collapse: collapse;
            width: 100%;
        }

        table.dataTable th, table.dataTable td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        table.dataTable thead th {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.3) !important;

        }
    </style>

<script type="text/javascript">
$(document).ready(function(){

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

    var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[3, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "50vh",
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


    
	var mapOption = {center: [4.682523136221837, -74.1092804182811], zoom: 12, zoomControl:true}
	var map = new L.map('openMap', mapOption);
	map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.boxZoom.disable();
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        subdomains: 'abcd',
        maxZoom: 19,
        minZoom: 10
    }).addTo(map);

    @foreach($datos as $data)
    	@foreach($data['datos'] as $key => $value)
		    @php
		        $coordenadas = explode(",", $value->coordenadas);
		        $lat = trim($coordenadas[0]); // Asegúrate de que las coordenadas no tengan espacios
		        $lng = trim($coordenadas[1]); // Asegúrate de que las coordenadas no tengan espacios
		    @endphp
		        // Añadir el marcador al mapa
		        L.marker([{{ $lat }}, {{ $lng }}])
		            .addTo(map)
		            .bindPopup("Empresa: {{ $value->propietario }} <br> Direccion: {{ $value->direccion }} <br> Sentido: {{ $sentidos[$value->sentidos_id]}}<br>");
		@endforeach
	@endforeach

});


</script>
