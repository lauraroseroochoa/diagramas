<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;">
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-4 m-0 p-0 me-3 ">
                listado clientes
                <table id="listadoClientes" class="datatable5 table table-striped w-100" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th class="border pt-0 pb-0" style="width:78%;">Cliente</th>
                            <th class="border pt-0 pb-0">Empresa</th>
                            <th class="border pt-0 pb-0">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $key => $value)
                            <tr>
                                <td style="width:50%;">{{ ucfirst(strtolower($value))}}</td>
                                <td>{!!  implode('<br>',array_keys($comercializadorXcliente[$key])) !!}</td>
                                <td style="width:10%;">
                                    <i class="fa-solid fa-eye" style="font-size:14px;" onclick="cargarInformacion({{$key}})"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="border pt-0 pb-0" ></td>
                            <td class="border pt-0 pb-0"></td>
                            <td class="border pt-0 pb-0"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col m-0 p-0 ">
                <div class="row p-0 m-0">
                    <div class="col-12 m-0 p-0 ">
                        <div class="row p-0 m-0 ps-2 border">
                            <div class="col-12 p-0 m-0 text-danger ">Anunciantes por cliente</div>
                            <div class="col-7 m-0 p-0 me-2" style="height:30vh;">
                                <table id="tableAnunciantes" class="datatable2 table table-striped w-100" style="font-size: 12px;">
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
                                            <td class="border pt-0 pb-0">Total</td>
                                            <td class="border pt-0 pb-0"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-4 m-0 p-0">
                                <div id="graficoAnunciantes" style="height:30vh;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 m-0 p-0">
                        <div class="row p-0 m-0 ps-2 border">
                            <div class="col-12 p-0 m-0 text-danger">Productos por cliente</div>
                            <div class="col-7 m-0 p-0 me-2" style="height:30vh;">
                                <table id="tableProductos" class="datatable3 table table-striped w-100" style="font-size: 12px;">
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
                                            <td class="border pt-0 pb-0">Total</td>
                                            <td class="border pt-0 pb-0"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col m-0 p-0">
                                <div id="graficoProductos" style="height:30vh;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 m-0 p-0">
                        <div class="row p-0 m-0 ps-2 border">
                            <div class="col-12 p-0 m-0 text-danger">Comercializadores por cliente</div>
                            <div class="col-7 m-0 p-0 me-2" style="height:30vh;">
                                <table id="tableComercializadores" class="datatable4 table table-striped w-100" style="font-size: 12px;">
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
                                            <td class="border pt-0 pb-0">Total</td>
                                            <td class="border pt-0 pb-0"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col m-0 p-0">
                                <div id="graficoComercializadores" style="height:30vh;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col border container-fluid me-0 pe-0" id="openMap"></div>
</div>
<script type="text/javascript">

    var table2 = setupDataTable('datatable2','20vh');
    var table3 = setupDataTable('datatable3','20vh');
    var table4 = setupDataTable('datatable4','20vh');
    var table5 = setupDataTable('datatable5','69vh');

    function setupDataTable(selector,tamano) {
        var table = $('.'+selector).DataTable({
            paging: false,
            order: [[1, 'desc']],
            dom: 'Brtp <"actions">',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: true,
            scrollY:tamano,
            scrollCollapse: true,
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
        table.buttons().remove();
        addSearchInput(table, selector);
        return table;
    }

    function addSearchInput(table, selector) {
        $('.'+selector + ' thead tr:eq(0) th').each(function (i) {
            $(this).append('<input type="text" placeholder="Buscar: " style="width:100%; font-size:11px; line-height:15px; border:none; border:1px solid #f2f2f2;" />');
            $('input', this).on('keyup change', function () {
                if (table.column(i).search() !== this.value) {
                    table.column(i).search(this.value).draw();
                }
            });
        });
    }

    var mapOption = {center: [4.682523136221837, -74.1092804182811], zoom: 12, zoomControl:true};
    globalThis.map = new L.map('openMap', mapOption);
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.boxZoom.disable();
    var layer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {subdomains: 'abcd', maxZoom: 19, minZoom: 10});
    map.addLayer(layer);

    var redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });


    function resetMap(data) {
        map.eachLayer(function (layer) {
            if (layer instanceof L.Marker) {
                map.removeLayer(layer);
            }
        });
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                var coordenadas = data[key]['coordenadas'].split(',');
                var lat = coordenadas[0].trim();
                var lng = coordenadas[1].trim();

                L.marker([lat, lng], {icon: redIcon})
                    .addTo(map)
                    .bindPopup("Empresa: " + data[key]['direccion'] + "<br>");
            }
        }
    }

    function cargarInformacion(cliente){
        var clientes = <?php echo json_encode($clientes); ?>;
        var anunciantesXcliente = <?php echo json_encode($anunciantesXcliente); ?>;
        var productosXcliente = <?php echo json_encode($productoXcliente); ?>;
        var comercializadoresXcliente = <?php echo json_encode($comercializadorXcliente); ?>;
        var lugaresXcliente = <?php echo json_encode($lugaresXcliente); ?>;

        clearTableAndGraph('#tableAnunciantes', '#graficoAnunciantes', anunciantesXcliente[cliente]);
        clearTableAndGraph('#tableProductos', '#graficoProductos', productosXcliente[cliente]);
        clearTableAndGraph('#tableComercializadores', '#graficoComercializadores', comercializadoresXcliente[cliente]);
        console.log(lugaresXcliente);
        resetMap(lugaresXcliente[cliente]);
    };

    function clearTableAndGraph(tableSelector, chartSelector, data) {
        var table = $(tableSelector).DataTable();
        table.clear();
        var categories = [];
        var values = [];

        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                categories.push(key);
                values.push(data[key]['total']);
                table.row.add([
                    key,
                    data[key]['total']
                ]);
            }
        }
        table.draw();
        renderChart(chartSelector, categories, values);
    }

    function renderChart(selector, categories, values) {
        Highcharts.chart(selector.substr(1), {
            chart: {type: 'column'},
            title: {text: ''},
            xAxis: {categories: categories},
            yAxis: {min: 0, title: {text: 'Total'}},
            series: [{
                name: 'Total',
                data: values
            }]
        });
    }
          
</script>
