<div class="row p-0 m-0" style="height: 72vh; max-height: 72vh; overflow:hidden; border:0px solid red;"  >
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-12 m-0 p-0 mb-2 ">
                <select class="form-control select2 bg-white " id="grupo">
                    <option value="">Seleccionar grupo</option>
                    @foreach($grupos as $key => $value)
                        <option value="{{$key}}" {{ isset($_GET['id']) && $_GET['id']==$key?'selected':'' }}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 m-0 p-0 me-2  ">
                <div class="row p-0 m-0 ps-2 border bg-white">
                    <div class="col-12 p-0 m-0 text-dos pt-1">Clientes por grupo</div>
                    <div class="col-7 m-0 p-0 me-2 " style="height:30vh; margin-top: -24px !important;">
                        <table id="tableClientes" class="datatable1 table table-striped  w-100 " style="font-size: 12px;" >
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Clientes</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border pt-0 pb-0" style="width:78%;">TOTAL</td>
                                    <td class="border pt-0 pb-0">Elementos</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col m-0 p-0 ">
                        <div id="graficoClientes" style="height:30vh;"></div>
                    </div>
                </div>
                <div class="row p-0 m-0  ps-2 border bg-white mt-2">
                    <div class="col-12 p-0 m-0 text-dos pt-1">Anunciantes por grupo</div>
                    <div class="col-7 m-0 p-0 me-2 " style="height:30vh; margin-top: -24px !important;">
                        <table id="tableAnunciantes" class="datatable2 table table-striped  w-100 " style="font-size: 12px;" >
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Clientes</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border pt-0 pb-0" style="width:78%;">TOTAL</td>
                                    <td class="border pt-0 pb-0">Elementos</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col m-0 p-0 ">
                        <div id="graficoAnunciantes" style="height:30vh;"></div>
                    </div>
                </div>
            </div>
            <div class="col m-0 p-0">
                <div class="row p-0 m-0  ps-2 border bg-white">
                    <div class="col-12 p-0 m-0 text-dos pt-1">Productos por grupo</div>
                    <div class="col-7 m-0 p-0 me-2 " style="height:30vh; margin-top: -24px !important;">
                        <table id="tableProductos" class="datatable3 table table-striped  w-100 " style="font-size: 12px;" >
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Clientes</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border pt-0 pb-0" style="width:78%;">TOTAL</td>
                                    <td class="border pt-0 pb-0">Elementos</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col m-0 p-0 ">
                        <div id="graficoProductos" style="height:30vh;"></div>
                    </div>
                </div>
                <div class="row p-0 m-0 ps-2  border bg-white mt-2">
                    <div class="col-12 p-0 m-0 text-dos pt-1">Comercializadores por grupo</div>
                    <div class="col-7 m-0 p-0 me-2 " style="height:30vh; margin-top: -24px !important;">
                        <table id="tableComercializadores" class="datatable4 table table-striped  w-100 " style="font-size: 12px;" >
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Clientes</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border pt-0 pb-0" style="width:78%;">TOTAL</td>
                                    <td class="border pt-0 pb-0">Elementos</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col m-0 p-0 ">
                        <div id="graficoComercializadores" style="height:30vh;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col border container-fluid me-0 pe-0"  id="openMap"></div>
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
    $('.datatable3 thead tr:eq(0) th').each( function (i) {
        $(this).append( '<input type="text" placeholder="Buscar: " style="width:100%; display:block; font-size:11px; line-height:15px; margin:1px 0px 0px 0px; border:none; border:1px solid #f2f2f2;" />'  );
        $( 'input', this ).on( 'keyup change', function () {
          if ( table3.column(i).search() !== this.value ) {
              table3
                  .column(i)
                  .search( this.value )
                  .draw();
          }
        } );
    });
    $('.datatable4 thead tr:eq(0) th').each( function (i) {
        $(this).append( '<input type="text" placeholder="Buscar: " style="width:100%; display:block; font-size:11px; line-height:15px; margin:1px 0px 0px 0px; border:none; border:1px solid #f2f2f2;" />'  );
        $( 'input', this ).on( 'keyup change', function () {
          if ( table4.column(i).search() !== this.value ) {
              table4
                  .column(i)
                  .search( this.value )
                  .draw();
          }
        } );
    });

    var table1 = $('.datatable1').DataTable({
        paging: false,
        order: [[1, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "20vh",
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

    var table2 = $('.datatable2').DataTable({
        paging: false,
        order: [[1, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "20vh",
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

    var table3 = $('.datatable3').DataTable({
        paging: false,
        order: [[1, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "20vh",
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

    var table4 = $('.datatable4').DataTable({
        paging: false,
        order: [[1, 'desc']],
        dom: 'Brtp <"actions"> ',
        orderCellsTop: true,
        fixedHeader: true,
        scrollX: true,
        scrollY: "20vh",
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

    var grupo = '';

    $("#grupo").on('change', function(){
        grupo = this.value;
        cargarInfo(grupo);
    });


    $(document).ready(function(){
    @if(isset($_GET['id']))
        grupo = {{$_GET['id']}};
        cargarInfo(grupo);
    @endif
    });


    function cargarInfo(grupo){
        var grupos = <?php echo json_encode($grupos); ?>;
        var clienteXgrupo = <?php echo json_encode($clienteXgrupo); ?>;
        var anunciantesXgrupo = <?php echo json_encode($anunciantesXgrupo); ?>;
        var productoXgrupo = <?php echo json_encode($productoXgrupo); ?>;
        var comercializadorXgrupo = <?php echo json_encode($comercializadorXgrupo); ?>;
        var lugaresXgrupo = <?php echo json_encode($lugaresXgrupo); ?>;

        var categorias1 = [];
        var valores1 = [];
        var tableClientes = $('#tableClientes').DataTable();
        tableClientes.clear(); // Limpia el contenido de la tabla

        var categorias2 = [];
        var valores2 = [];
        var tableAnunciantes = $("#tableAnunciantes").DataTable();
        tableAnunciantes.clear(); // Limpia el contenido de la tabla

        var categorias3 = [];
        var valores3 = [];
        var tableProductos = $("#tableProductos").DataTable();
        tableProductos.clear(); // Limpia el contenido de la tabla

        var categorias4 = [];
        var valores4 = [];
        var tableComercializadores = $("#tableComercializadores").DataTable();
        tableComercializadores.clear(); // Limpia el contenido de la tabla

        for (var key in clienteXgrupo[grupos[grupo]])
        {
            if (clienteXgrupo[grupos[grupo]].hasOwnProperty(key)) {
                categorias1.push(key);
                valores1.push(clienteXgrupo[grupos[grupo]][key]['total']);
                tableClientes.row.add([
                    key.toUpperCase(),
                    clienteXgrupo[grupos[grupo]][key]['total']
                ]);

            }
        }
        tableClientes.draw();

        for (var key in anunciantesXgrupo[grupos[grupo]])
        {

            if (anunciantesXgrupo[grupos[grupo]].hasOwnProperty(key)) {
                categorias2.push(key);
                valores2.push(anunciantesXgrupo[grupos[grupo]][key]['total']);
                tableAnunciantes.row.add([
                    key.toUpperCase(),
                    anunciantesXgrupo[grupos[grupo]][key]['total']
                ]);

            }
        }
        tableAnunciantes.draw();

        for (var key in productoXgrupo[grupos[grupo]])
        {

            if (productoXgrupo[grupos[grupo]].hasOwnProperty(key)) {
                categorias3.push(key);
                valores3.push(productoXgrupo[grupos[grupo]][key]['total']);
                tableProductos.row.add([
                    key.toUpperCase(),
                    productoXgrupo[grupos[grupo]][key]['total']
                ]);

            }
        }
        tableProductos.draw();

        for (var key in comercializadorXgrupo[grupos[grupo]])
        {

            if (comercializadorXgrupo[grupos[grupo]].hasOwnProperty(key)) {
                categorias4.push(key);
                valores4.push(comercializadorXgrupo[grupos[grupo]][key]['total']);
                tableComercializadores.row.add([
                    key.toUpperCase() === "MARKETMEDIOS"
                        ? `<span style="color: red;">${key.toUpperCase()}</span>`
                        : key.toUpperCase(),
                    comercializadorXgrupo[grupos[grupo]][key]['total']
                ]);



            }
        }
        tableComercializadores.draw();


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
        for (var key in lugaresXgrupo[grupos[grupo]])
        {
            var coordenadas = lugaresXgrupo[grupos[grupo]][key]['coordenadas'].split(',');
            var lat = coordenadas[0].trim();
            var lng = coordenadas[1].trim();
            L.marker([lat, lng])
                .addTo(map)
                .bindPopup("Empresa: "+lugaresXgrupo[grupos[grupo]][key]['direccion']+"<br>");
        }


        var datos = valores1.map((valor, index) => ({
            name: categorias1[index],
            y: valor
        }));

        // Extraer categorías y valores para el gráfico de barras
        var categorias1 = datos.map(data => data.name);
        var valores1 = datos.map(data => data.y);


        Highcharts.chart('graficoClientes', {
            chart: {
                type: 'bar', // Tipo de gráfico
                width: null, // El ancho se ajustará automáticamente
                height: null // La altura se ajustará automáticamente
            },
            title: {
                text: null // Elimina el título
            },
            legend: {
                enabled: false // Desactiva la leyenda
            },
            credits: {
                enabled: false // Desactiva los créditos de Highcharts
            },
            xAxis: {
                categories: categorias1, // Usa las categorías para el eje X
                title: {
                    text: null // Elimina el título del eje X
                },
                labels: {
                    enabled: false // Oculta las etiquetas del eje X
                }
            },
            yAxis: {
                title: {
                    text: 'Clientes' // Título del eje Y
                },
                min: 0 // Establece el mínimo en 0
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b>: ' + this.y; // Formato del tooltip
                }
            },
            series: [{
                name: 'Clientes', // Nombre de la serie
                data: valores1,
                dataLabels: {
                    enabled: true, // Habilita las etiquetas de datos
                    formatter: function() {
                        return this.y; // Muestra el valor de cada barra
                    },
                    style: {
                        fontSize: '10px', // Ajusta el tamaño del texto aquí
                        color: '#000000', // Color del texto
                        textOutline: 'none' // Elimina el contorno del texto
                    },
                    verticalAlign: 'bottom', // Posiciona las etiquetas en la parte superior de las barras
                    align: 'left' // Centra las etiquetas horizontalmente
                }
            }]
        });


        var datos = valores2.map((valor, index) => ({
            name: categorias2[index],
            y: valor
        }));

        // Extraer categorías y valores para el gráfico de barras
        var categorias2 = datos.map(data => data.name);
        var valores2 = datos.map(data => data.y);

        Highcharts.chart('graficoAnunciantes', {
            chart: {
                type: 'bar', // Tipo de gráfico
                width: null, // El ancho se ajustará automáticamente
                height: null // La altura se ajustará automáticamente
            },
            title: {
                text: null // Elimina el título
            },
            legend: {
                enabled: false // Desactiva la leyenda
            },
            credits: {
                enabled: false // Desactiva los créditos de Highcharts
            },
            xAxis: {
                categories: categorias2, // Usa las categorías para el eje X
                title: {
                    text: null // Elimina el título del eje X
                },
                labels: {
                    enabled: false // Oculta las etiquetas del eje X
                }
            },
            yAxis: {
                title: {
                    text: 'Anunciantes' // Título del eje Y
                },
                min: 0 // Establece el mínimo en 0
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b>: ' + this.y; // Formato del tooltip
                }
            },
            series: [{
                name: 'Anunciantes', // Nombre de la serie
                data: valores2,
                dataLabels: {
                    enabled: true, // Habilita las etiquetas de datos
                    formatter: function() {
                        return this.y; // Muestra el valor de cada barra
                    },
                    style: {
                        fontSize: '10px', // Ajusta el tamaño del texto aquí
                        color: '#000000', // Color del texto
                        textOutline: 'none' // Elimina el contorno del texto
                    },
                    verticalAlign: 'bottom', // Posiciona las etiquetas en la parte superior de las barras
                    align: 'left' // Centra las etiquetas horizontalmente
                }
            }]
        });



        var datos = valores3.map((valor, index) => ({
            name: categorias3[index],
            y: valor
        }));

        // Extraer categorías y valores para el gráfico de barras
        var categorias3 = datos.map(data => data.name);
        var valores3 = datos.map(data => data.y);

        Highcharts.chart('graficoProductos', {
            chart: {
                type: 'bar', // Tipo de gráfico
                width: null, // El ancho se ajustará automáticamente
                height: null // La altura se ajustará automáticamente
            },
            title: {
                text: null // Elimina el título
            },
            legend: {
                enabled: false // Desactiva la leyenda
            },
            credits: {
                enabled: false // Desactiva los créditos de Highcharts
            },
            xAxis: {
                categories: categorias3, // Usa las categorías para el eje X
                title: {
                    text: null // Elimina el título del eje X
                },
                labels: {
                    enabled: false // Oculta las etiquetas del eje X
                }
            },
            yAxis: {
                title: {
                    text: 'Productos' // Título del eje Y
                },
                min: 0 // Establece el mínimo en 0
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b>: ' + this.y; // Formato del tooltip
                }
            },
            series: [{
                name: 'Productos', // Nombre de la serie
                data: valores3,
                dataLabels: {
                    enabled: true, // Habilita las etiquetas de datos
                    formatter: function() {
                        return this.y; // Muestra el valor de cada barra
                    },
                    style: {
                        fontSize: '10px', // Ajusta el tamaño del texto aquí
                        color: '#000000', // Color del texto
                        textOutline: 'none' // Elimina el contorno del texto
                    },
                    verticalAlign: 'bottom', // Posiciona las etiquetas en la parte superior de las barras
                    align: 'left' // Centra las etiquetas horizontalmente
                }
            }]
        });




        var datos = valores4.map((valor, index) => ({
            name: categorias4[index],
            y: valor
        }));

        // Extraer categorías y valores para el gráfico de barras
        var categorias4 = datos.map(data => data.name);
        var valores4 = datos.map(data => data.y);

        Highcharts.chart('graficoComercializadores', {
            chart: {
                type: 'bar', // Tipo de gráfico
                width: null, // El ancho se ajustará automáticamente
                height: null // La altura se ajustará automáticamente
            },
            title: {
                text: null // Elimina el título
            },
            legend: {
                enabled: false // Desactiva la leyenda
            },
            credits: {
                enabled: false // Desactiva los créditos de Highcharts
            },
            xAxis: {
                categories: categorias4, // Usa las categorías para el eje X
                title: {
                    text: null // Elimina el título del eje X
                },
                labels: {
                    enabled: false // Oculta las etiquetas del eje X
                }
            },
            yAxis: {
                title: {
                    text: 'Comercializadores' // Título del eje Y
                },
                min: 0 // Establece el mínimo en 0
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b>: ' + this.y; // Formato del tooltip
                }
            },
            series: [{
                name: 'Comercializadores', // Nombre de la serie
                data: valores4,
                dataLabels: {
                    enabled: true, // Habilita las etiquetas de datos
                    formatter: function() {
                        return this.y; // Muestra el valor de cada barra
                    },
                    style: {
                        fontSize: '10px', // Ajusta el tamaño del texto aquí
                        color: '#000000', // Color del texto
                        textOutline: 'none' // Elimina el contorno del texto
                    },
                    verticalAlign: 'bottom', // Posiciona las etiquetas en la parte superior de las barras
                    align: 'left' // Centra las etiquetas horizontalmente
                }
            }]
        });



    }



</script>