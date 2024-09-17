<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;">
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-12 m-0 p-0 mb-2 border">
                <select class="form-control select2" id="cliente">
                    <option value="">Seleccionar cliente</option>
                    @foreach($clientes as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
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
                        </table>
                    </div>
                    <div class="col m-0 p-0">
                        <div id="graficoComercializadores" style="height:30vh;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col border container-fluid me-0 pe-0" id="openMap"></div>
</div>
<script type="text/javascript">

    var table2 = setupDataTable('.datatable2');
    var table3 = setupDataTable('.datatable3');
    var table4 = setupDataTable('.datatable4');

    function setupDataTable(selector) {
        var table = $(selector).DataTable({
            paging: false,
            order: [[1, 'desc']],
            dom: 'Brtp <"actions">',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: true,
            scrollY: "20vh",
            scrollCollapse: true
        });
        table.buttons().remove();
        addSearchInput(table, selector);
        return table;
    }

    function addSearchInput(table, selector) {
        $(selector + ' thead tr:eq(0) th').each(function (i) {
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

    
    $("#cliente").on('change', function(){
        var cliente = this.value;
        var clientes = <?php echo json_encode($clientes); ?>;
        var anunciantesXcliente = <?php echo json_encode($anunciantesXcliente); ?>;
        var productosXcliente = <?php echo json_encode($productoXcliente); ?>;
        var comercializadoresXcliente = <?php echo json_encode($comercializadorXcliente); ?>;
        var lugaresXcliente = <?php echo json_encode($lugaresXcliente); ?>;

        clearTableAndGraph('#tableAnunciantes tbody', '#graficoAnunciantes', anunciantesXcliente[cliente]);
        clearTableAndGraph('#tableProductos tbody', '#graficoProductos', productosXcliente[cliente]);
        clearTableAndGraph('#tableComercializadores tbody', '#graficoComercializadores', comercializadoresXcliente[cliente]);
        console.log(lugaresXcliente);
        resetMap(lugaresXcliente[cliente]);
    });

    function clearTableAndGraph(tableSelector, chartSelector, data) {
        var table = $(tableSelector);
        table.empty();
        var categories = [];
        var values = [];

        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                categories.push(key);
                values.push(data[key]['total']);
                var row = '<tr><td class="border pt-0 pb-0">'+key+'</td><td class="border pt-0 pb-0">'+data[key]['total']+'</td></tr>';
                table.append(row);
            }
        }

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
