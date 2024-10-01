<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;"  >
    <div class="col m-0 p-0 me-3 container-fluid ">
        <table id="pauta" class="datatable1 table table-striped tableFont w-100 "  style="font-size: 12px;">
        	<thead>
        		<tr>
        			<th class="border">Grupo</th>
        			<th class="border">Cliente</th>
        			<th class="border">Anunciante</th>
        			<th class="border">Producto</th>
        			<th class="border">Lugar</th>
        			<th class="border">Elemento</th>
        			<th class="border">Foto</th>
        		</tr>
        	</thead>
        	<tbody>
        		@foreach($datos as $key => $value)
        			<tr>
	        			<td class="border">{{ $value->grupo }}</td>
	        			<td class="border">{{ $value->cliente }}</td>
	        			<td class="border">{{ $value->anunciante }}</td>
	        			<td class="border">{{ $value->producto }}</td>
	        			<td class="border">{{ $value->direccion }}</td>
	        			<td class="border">{{ isset($tipoPublicidades[$value->tipopublicidades_id])?$tipoPublicidades[$value->tipopublicidades_id]:''  }}</td>
	        			<td class="border"><i class="fa-solid fa-image" onclick="showPhoto('{{$value->foto2 }}','Empresa: {{ $empresas[$value->comercializadorId]->descripcion }} <br>Direccion: {{ $value->direccion }} <br>Fecha: {{ $value->created_at }}');"></i></td>
	        		</tr>
        		@endforeach
        	</tbody>
        	
        </table>
    </div>
    <div class="col-3 border container-fluid me-0 pe-0"  id="openMap"></div>
    
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

    var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[0, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "54vh",
        scrollCollapse: true,
        buttons: [
            'excel'
        ]
    });

    var mapOption = {center: [4.682523136221837, -74.1092804182811], zoom: 12, zoomControl:true}
    globalThis.map = new L.map('openMap', mapOption);
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.boxZoom.disable();
    var layer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        subdomains: 'abcd',
        maxZoom: 19,
        minZoom: 10
    });
    map.addLayer(layer);

    var redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41], // Tamaño del icono
        iconAnchor: [12, 41], // Punto del icono que se alineará con las coordenadas
        popupAnchor: [1, -34] // Punto desde el que se mostrará el popup
    });
    


    @foreach($datos as $value)
        
            @php
                $lat = '';
                $lng = '';
                
                $coordenadas = explode(",", $value->coordenadas);
                if(count($coordenadas)>1){
                    $lat = trim($coordenadas[0]); // Asegúrate de que las coordenadas no tengan espacios
                    $lng = trim($coordenadas[1]); // Asegúrate de que las coordenadas no tengan espacios        
                }
                
                
            @endphp
                // Añadir el marcador al mapa
                L.marker([{{ $lat }}, {{ $lng }}])
                    .addTo(map)
                    .bindPopup("Empresa: {{ $empresas[$value->comercializadorId]->descripcion }}  <br>Direccion: {{$value->direccion }}");


        
    @endforeach



</script>