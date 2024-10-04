<div class="row">
    <div class="col-7" style="height: 65vh; max-height: 65vh;  ">
        <div class="mt-3 " style="margin-bottom:-32px;">
            <h5 class="text-dos">Cantidad {{ $descr }}</h5>
        </div>
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
                @foreach ($datos as $key => $value)
                    @php
                        $clase = '';
                        if ($key == 2) {
                            $clase = 'text-danger';
                        }
                    @endphp
                    <tr>
                        <td class="border {{ $clase }}">{{ strtoupper($empresas[$key]) }}</td>
                        <td class="border"><a href="#"
                                onclick="graficarMarkers({{ $key }},'datosTradicional');">{{ $value['tradicional'] }}</a>
                        </td>
                        <td class="border"><a href="#"
                                onclick="graficarMarkers({{ $key }},'datosLed');">{{ $value['led'] }}</a></td>
                        <td class="border"><a href="#"
                                onclick="graficarMarkers({{ $key }},'datos');">{{ $value['total'] }}</a></td>
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
        height: 65vh;
        /* Ajusta la altura según tus necesidades */
        overflow: hidden;
    }

    .dataTables_scroll {
        height: 100%;
        overflow: hidden;
    }

    .dataTables_scrollBody {
        overflow-y: auto;
        height: calc(100% - 40px);
        /* Ajusta según la altura del header y footer */
    }

    .dataTables_scrollHead,
    .dataTables_scrollFoot {
        position: sticky;
        background: white;
        /* Asegura que el fondo sea blanco */
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

    table.dataTable th,
    table.dataTable td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    table.dataTable thead th {
        background: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.3) !important;

    }
</style>

<script type="text/javascript">
    $(document).ready(function() {




        var table1 = $('.datatable1').DataTable({
            paging: false,
            order: [
                [3, 'desc']
            ],
            dom: 'Brtp <"actions"> ',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: true,
            scrollY: "50vh",
            scrollCollapse: true,
            buttons: [
                'excel'
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Calcula el total de cada columna
                var totalTradicional = api.column(1, {
                    page: 'current'
                }).nodes().map(function(td) {
                    return parseFloat($(td).text()) || 0; 
                }).reduce(function(a, b) {
                    return a + b;
                }, 0);

                var totalLed = api.column(2, {
                    page: 'current'
                }).nodes().map(function(td) {
                    return parseFloat($(td).text()) || 0; 
                }).reduce(function(a, b) {
                    return a + b;
                }, 0);

                var totalGeneral = api.column(3, {
                    page: 'current'
                }).nodes().map(function(td) {
                    return parseFloat($(td).text()) || 0; 
                }).reduce(function(a, b) {
                    return a + b;
                }, 0);

                $(api.column(1).footer()).html(totalTradicional);
                $(api.column(2).footer()).html(totalLed);
                $(api.column(3).footer()).html(totalGeneral);

            }
        });


        var mapOption = {
            center: [4.682523136221837, -74.1092804182811],
            zoom: 12,
            zoomControl: true
        }
        globalThis.map = new L.map('openMap', mapOption);
        map.touchZoom.disable();
        map.doubleClickZoom.disable();
        map.boxZoom.disable();
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            subdomains: 'abcd',
            maxZoom: 19,
            minZoom: 10
        }).addTo(map);

        var redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41], // Tamaño del icono
            iconAnchor: [12, 41], // Punto del icono que se alineará con las coordenadas
            popupAnchor: [1, -34] // Punto desde el que se mostrará el popup
        });

        @foreach ($datos as $data)
            @foreach ($data['datos'] as $key => $value)
                @php
                    $lat = '';
                    $lng = '';

                    $coordenadas = explode(',', $value->coordenadas);
                    if (count($coordenadas) > 1) {
                        $lat = trim($coordenadas[0]);
                        $lng = trim($coordenadas[1]);
                    }

                @endphp
                // Añadir el marcador al mapa
                L.marker([{{ $lat }}, {{ $lng }}])
                    .addTo(map)
                    .bindPopup(
                        "Empresa: {{ $value->propietario }} <br>  Direccion: <br>   Sentido: {{ $sentidos[$value->sentidos_id] }}<br>"
                        );
            @endforeach
        @endforeach




    });

    var datos = <?php echo json_encode($datos); ?>;

    function graficarMarkers(idEmpresa, nombre) {
        //reset
        map.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                map.removeLayer(layer);
            }
        });
        //graficar
        var data = datos[idEmpresa][nombre];
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                var coordenadas = data[key]['coordenadas'].split(',');
                var lat = coordenadas[0].trim();
                var lng = coordenadas[1].trim();

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup("Empresa: " + data[key]['direccion'] + "<br>");
            }
        }

    }
</script>
