<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;"  >
    <div class="col m-0 p-0 me-3">
        <div class="m-0 mb-2 ">        
            <select class="form-control select2  bg-white " id="producto">
                <option value="">Seleccionar marca/producto</option>
                @foreach($productos as $key => $value)
                    <option value="{{$key}}" {{ isset($_GET['id']) && $_GET['id']==$key?'selected':'' }}>{{$value}}</option>
                @endforeach
            </select>
        </div>
        <div class="row mb-2 ps-1">
            <div class="col border ms-2   bg-white" style="height:30vh;">
                <div class="text-dos mt-1">Cantidad por empresa</div>
                <div id="cantidadXempresa" style="width: 100%; height: 27vh;"></div>
            </div>
            <div class="col-4 m-0 ">
                <div class=" mb-1" style="width: 100%; height: 9vh;">
                    <table id="tableProducto" class="datatable2 table table-striped  w-100  bg-white" style="font-size: 12px;" >
                        <thead>
                            <tr>
                                <th class="border pt-0 pb-0">Producto</th>
                                <th class="border pt-0 pb-0">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div class="border bg-white">
                    <div class="text-dos mt-1">Participación</div>
                    <div id="participacion"  style="width: 100%; height: 18vh;"></div>
                </div>
            </div>
        </div>
        <div class="row ps-1">
            <div class="col  " style="height:32vh;">
                <div class="text-dos mt-1 ms-2"  style="margin-bottom:-20px;">Referencias</div>
                <table id="cantidad" class="datatable1 table table-striped tableFont w-100  bg-white">
                    <thead>
                        <tr>
                            <th class="border">Empresa</th>
                            <th class="border">Ubicacion</th>
                            <th class="border">Referencia</th>
                            <th class="border">Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-4 border container-fluid me-0 pe-0"  id="openMap"></div>
    
</div>
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
        order: [[0, 'asc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "20vh",
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

    

    var datos2 = <?php echo json_encode($datos2); ?>;


    function updateTable(productoId) {
        var productoData = datos2[productoId];
        var tableBody = $("#cantidad tbody");
        tableBody.empty(); // Limpia el contenido de la tabla

        


        var mapOption = {center: [4.682523136221837, -74.1092804182811], zoom: 12, zoomControl:true}
        if(typeof map != 'undefined'){
            map.remove();
            map = null;
            // Creating a map object
            globalThis.map = new L.map('openMap', mapOption);
        }else{
            // Creating a map object
            globalThis.map = new L.map('openMap', mapOption);
        }
        var layer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            subdomains: 'abcd',
            maxZoom: 19,
            minZoom: 10
        });
        map.addLayer(layer);


        if (productoData) {
            for (var key2 in productoData) {
                if (productoData.hasOwnProperty(key2)) {

                    var references = productoData[key2]['referencias'];
                    var ubicaciones = productoData[key2]['ubicaciones'];
                    for (var key3 in references) {
                        if (references.hasOwnProperty(key3)) {
                            var items = references[key3];
                            for (var key4 in items) {
                                if (items.hasOwnProperty(key4)) {
                                    var row = '<tr>' +
                                        '<td class="border">' + key2 + '</td>' +
                                        '<td class="border">' + key3 + '</td>' +
                                        '<td class="border">' + key4 + '</td>' +
                                        '<td class="border"><i class="fa-solid fa-image" onclick="showPhoto(\'' + items[key4]['foto'] + '\',\'' + key2 + ' - '+key3+' - ' + items[key4]['fecha'] + ' \');"></i></td>' +
                                        '</tr>';
                                    tableBody.append(row);
                                }
                            }
                        }
                    }

                    for (var key5 in ubicaciones) {
                        if (ubicaciones.hasOwnProperty(key5)) {
                            console.log(key5);
                            var coordenadas = key5.split(',');
                            var lat = coordenadas[0].trim();
                            var lng = coordenadas[1].trim();
                            L.marker([lat, lng])
                                .addTo(map)
                                .bindPopup("Empresa: "+key2);
                        }
                    }
                }
            }
        }
    }


    function cargarInfo(productoId){
        var productoData = datos2[productoId];
        var datos3 = <?php echo json_encode($datos3); ?>;
        var productos = <?php echo json_encode($productos); ?>;
        var totalParticipacion = {{ ($totalLed+$totalTradicional) }};
        var cantidad = datos3[productoId];


        if (productoData) {

            var tableBody2 = $("#tableProducto tbody");
            tableBody2.empty(); // Limpia el contenido de la tabla
            var row2 = '<tr>'+
                '<td class="border pt-0 pb-0">'+productos[productoId]+'</td>'+
                '<td class="border pt-0 pb-0">'+cantidad+'</td>'+
                '</tr>';
            tableBody2.append(row2);

            // Convertir productoData a formato adecuado para Highcharts
            var categorias = [];
            var valores = [];
            var colores = [];

            for (var key in productoData) {
                if (productoData.hasOwnProperty(key)) {
                    categorias.push(key);
                    valores.push(productoData[key]['total']);
                    colores.push('#' + productoData[key]['color']);
                }
            }

            var porcentajeProducto = (cantidad * 100 / totalParticipacion).toFixed(2);
            var porcentajePauta = (100 - (cantidad * 100 / totalParticipacion)).toFixed(2);


            Highcharts.chart('participacion', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 32
                    }
                },
                title: {
                    text: null
                },
                plotOptions: {
                    pie: {
                        innerSize: 33,
                        depth: 15,
                        dataLabels: {
                            formatter: function() {
                                return this.point.name + ': ' + this.y + '%';
                            }
                        }
                    }
                },
                series: [{
                    name: 'participación',
                    data: [
                        [productos[productoId], parseFloat(porcentajeProducto)],
                        ['Pauta al aire', parseFloat(porcentajePauta)]
                    ],
                    colorByPoint: true, // Esto permite usar colores diferentes para cada columna
                    colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
                }]
            });

            // Configurar el gráfico
            Highcharts.chart('cantidadXempresa', {
                chart: {
                    type: 'column',
                    // Configura el gráfico para adaptarse al contenedor
                    width: null, // El ancho se ajustará automáticamente
                    height: null, // La altura se ajustará automáticamente
                    options3d: {
                        enabled: true,
                        alpha: 0,
                        beta: 0,
                        depth: 28,
                        viewDistance: 25
                    }
                },
                title: {
                    text: null // Elimina el título
                },
                xAxis: {
                    categories: categorias,
                    title: {
                        text: null // Elimina el título del eje x
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null // Elimina el título del eje y
                    }
                },
                legend: {
                    enabled: false // Desactiva la leyenda
                },
                credits: {
                   enabled: false
                },
                series: [{
                    name: '',
                    data: valores,
                    colorByPoint: true, // Esto permite usar colores diferentes para cada columna
                    colors: ['#c0c0c0', '#4B9EBF', '#71c5d9'],
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            chart: {
                                width: null
                            }
                        }
                    }]
                }
            });

            updateTable(productoId);

        }
    }


    $("#producto").on('change', function(){
        var productoId = this.value;
        cargarInfo(productoId);
    });


    $(document).ready(function(){
    @if(isset($_GET['id']))
        id = {{$_GET['id']}};
        cargarInfo(id);
    @endif
    });

});


</script>
