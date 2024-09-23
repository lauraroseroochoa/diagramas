<div class="row p-0 m-0" style="height: 69vh; max-height: 69vh; overflow:hidden; border:0px solid red;">
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-12 m-0 p-0 mb-2 border">
                <select class="form-control select2" id="anunciante">
                    <option value="">Seleccionar Anunciante</option>
                    @foreach($anunciantes as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 m-0 p-0 mb-2 border">
                <div class="row p-0 m-0 ps-2 border">
                    <div class="col-12 p-0 m-0  text-danger ">Producto por anunciante</div>
                    <div class="col-7 m-0 p-0 me-2" style="height:40vh;">
                        <table id="tableProducto" class="datatable2 table table-striped w-100" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Productos</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-4 m-0 p-0 ">
                        <div id="graficoProducto" style="height:30vh;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 m-0 p-0 border ">
                <div class="row p-0 m-0 ps-2 border">
                    <div class="col-12 p-0 m-0 text-danger">Comercializador por anunciante</div>
                    <div class="col-7 m-0 p-0 me-2" style="height:50vh;">
                        <table id="tableComercializador" class="datatable3 table table-striped w-100" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th class="border pt-0 pb-0" style="width:78%;">Comercializador</th>
                                    <th class="border pt-0 pb-0">Elementos</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col m-0 p-0">
                        <div id="graficoComercializador" style="height:30vh;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col border container-fluid me-0 pe-0" id="openMap"></div>
</div>
<script type="text/javascript">

    var table2 = setupDataTable('datatable2');
    var table3 = setupDataTable('datatable3');

    function setupDataTable(selector) {
        var table = $('.'+ selector).DataTable({
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

    
    $("#anunciante").on('change', function(){
        var anunciante = this.value;
        var anunciantes = <?php echo json_encode($anunciantes); ?>;
        var productoXanunciante = <?php echo json_encode($productosXanunciante); ?>;
        var comercializadorXanunciante = <?php echo json_encode($comercializadoresXanunciante); ?>;
        var lugarXanunciante = <?php echo json_encode($lugaresXanunciante); ?>;

        clearTableAndGraph('#tableProducto tbody', '#graficoProducto', productoXanunciante[anunciante]);
        clearTableAndGraph('#tableComercializador tbody', '#graficoComercializador', comercializadorXanunciante[anunciante]);

        resetMap(lugarXanunciante[anunciante]);
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
