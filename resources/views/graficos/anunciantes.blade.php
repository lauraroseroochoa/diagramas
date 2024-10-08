<div class="row p-0 m-0" style="height: 72vh; max-height: 72vh; overflow:hidden; border:0px solid red;">
    <div class="col-9 m-0 p-0 me-3 ">
        <div class="row p-0 m-0">
            <div class="col-4 m-0 p-0 me-3 ">
                <div  class="text-dos mt-1" style="margin-bottom: -24px;">Listado anunciantes</div>
                <table id="listadoAnunciantes" class="datatable4 table table-striped w-100" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th class="border pt-0 pb-0" style="width:78%;">Anunciante</th>
                        <th class="border pt-0 pb-0">Empresa</th>
                        <th class="border pt-0 pb-0">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anunciantes as $key => $value)
                        <tr>
                            <td  style="width:50%;">{{ strtoupper($value)}}</td>
                            <td>{!!  implode('<br>',array_keys($comercializadoresXanunciante[$key])) !!}</td>
                            <td style="width:10%;">
                                <i class="fa-solid fa-eye" style="font-size:14px;" onclick="cargarInformacion({{$key}})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="border pt-0 pb-0" style="width:78%;"></td>
                        <td class="border pt-0 pb-0"></td>
                        <td class="border pt-0 pb-0"></td>
                    </tr>
                </tfoot>
                </table>
            </div>
            <div class="col m-0 p-0 ">
                <div  class="text-dos mt-1 pl-4" id="tituloAnunciante" >&nbsp;</div>
                <div class="row p-0 m-0">
                    <div class="col-12 m-0 p-0 mb-2 border mt-3  bg-white">
                        <div class="row p-0 m-0 ps-2 border">
                            <div  class="text-dos mt-1"  style="margin-bottom: -24px;">Producto por anunciante</div>
                            <div class="col-6 m-0 p-0 me-2" style="height:30vh;">
                                <table id="tableProducto" class="datatable2 table table-striped w-100" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th class="border pt-0 pb-0" style="width:78%;">Productos</th>
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
                            <div class="col-5 m-0 p-0 me-3 ">
                                <div id="graficoProducto" style="height:30vh;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 m-0 p-0 border bg-white ">
                        <div class="row p-0 m-0 ps-2 border">
                            <div  class="text-dos mt-1"  style="margin-bottom: -24px;">Comercializador por anunciante</div>
                            <div class="col-6 m-0 p-0 me-2" style="height:30vh;">
                                <table id="tableComercializador" class="datatable3 table table-striped w-100" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th class="border pt-0 pb-0" style="width:78%;">Comercializador</th>
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
                                <div id="graficoComercializador" style="height:30vh;"></div>
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

    var table2 = setupDataTable('datatable2','20vh',0);
    var table3 = setupDataTable('datatable3','20vh',0);
    var table4 = setupDataTable('datatable4','63vh',1);

    function setupDataTable(selector,tamano,exportar) {
        var table = $('.'+ selector).DataTable({
            paging: false,
            order: [[1, 'desc']],
            dom: 'Brtp <"actions">',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: false,
            scrollY: tamano,
            scrollCollapse: true,
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                // Calcula el total de cada columna
                var total = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                // Muestra los totales en el pie de página
                $(api.column(1).footer()).html(total);
            },
            buttons: [
                'excel'
            ],
        });

        
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

                
                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup("Empresa: " + data[key]['empresa'] + " <br>Direccion:" + data[key]['direccion'] + "<br>");
            }
        }
    }

    var anunciantes = <?php echo json_encode($anunciantes); ?>;
    
    function cargarInformacion(anunciante){

        var productoXanunciante = <?php echo json_encode($productosXanunciante); ?>;
        var comercializadorXanunciante = <?php echo json_encode($comercializadoresXanunciante); ?>;
        var lugarXanunciante = <?php echo json_encode($lugaresXanunciante); ?>;


        $('#tituloAnunciante').html('<span class="text-secondary">Anunciante seleccionado: </span><b>'+anunciantes[anunciante]+'</b>');

        clearTableAndGraph('#tableProducto', '#graficoProducto', productoXanunciante[anunciante]);
        clearTableAndGraph('#tableComercializador', '#graficoComercializador', comercializadorXanunciante[anunciante]);

        resetMap(lugarXanunciante[anunciante]);
    };

    $(document).ready(function(){
    @if(isset($_GET['id']))
        id = {{$_GET['id']}};
        $('.datatable4 thead tr th:first input').val(anunciantes[id]);
        $('#listadoAnunciantes').DataTable().column(0).search(anunciantes[id]).draw();
        cargarInformacion(id);
    @endif
    });

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
                    key.toUpperCase() === "MARKETMEDIOS"
                        ? `<span class="text-danger">${key.toUpperCase()}</span>`
                        : key.toUpperCase(),
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
