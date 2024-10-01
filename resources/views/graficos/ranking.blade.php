<div class="row">
    <div class="col-6  me-2">
        <div class="text-dos mt-1" style="margin-bottom:-24px;">Ranking Grupos</div>
        <table class="datatable2 table table-striped w-100" >
            <thead>
                <tr>
                    <th style="width: 45%;">Nombre del Grupo</th>
                    <th>Total Tradicional</th>
                    <th>Total LED</th>
                    <th>Total</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankingGrupos as $key => $grupos)
                    <tr>
                        <td>{{ strtoupper($grupos['nombre']) }}</td>
                        <td>{{ $grupos['tradicional'] }}</td>
                        <td>{{ $grupos['led'] }}</td>
                        <td>{{ $grupos['total'] }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verGruposAnunciantesClientes({{ $grupos['id'] }},'grupos')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th id="totalTradicional"></th>
                    <th id="totalLed"></th>
                    <th id="totalGeneral"></th>
                    <th ></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col  ">
        <div class="text-dos mt-1" style="margin-bottom:-24px;">Ranking Anunciantes</div>
        <table class="datatable2 table table-striped w-100" >
            <thead>
                <tr>
                    <th style="width: 45%;">Anunciante</th>
                    <th>Total Tradicional</th>
                    <th>Total LED</th>
                    <th>Total</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankingAnunciantes as $item)
                    <tr>
                        <td>{{ strtoupper($item['nombre']) }}</td>
                        <td>{{ $item['tradicional'] }}</td>
                        <td>{{ $item['led'] }}</td>
                        <td>{{ $item['total'] }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verGruposAnunciantesClientes({{ $item['id'] }},'anunciantes')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th id="totalTradicional"></th>
                    <th id="totalLed"></th>
                    <th id="totalGeneral"></th>
                    <th ></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row  mt-3">
    <div class="col-6  me-2">
        <div class="text-dos mt-1" style="margin-bottom:-24px;">Ranking Clientes</div>
        <table class="datatable2 table table-striped w-100" >
            <thead>
                <tr>
                    <th style="width: 45%;">Nombre del Cliente</th>
                    <th>Total Tradicional</th>
                    <th>Total LED</th>
                    <th>Total</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankingClientes as $cliente)
                    <tr>
                        <td>{{ strtoupper($cliente['nombre']) }}</td>
                        <td>{{ $cliente['totalTradicional'] }}</td>
                        <td>{{ $cliente['totalLed'] }}</td>
                        <td>{{ $cliente['total'] }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verGruposAnunciantesClientes({{ $cliente['id'] }},'clientes')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th id="totalTradicional"></th>
                    <th id="totalLed"></th>
                    <th id="totalGeneral"></th>
                    <th ></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col ">
        <div class="text-dos mt-1" style="margin-bottom:-24px;">Ranking productos</div>
        <table class="datatable2 table table-striped w-100" >
            <thead>
                <tr>
                    <th style="width: 45%;">Nombre del producto</th>
                    <th>Total Tradicional</th>
                    <th>Total LED</th>
                    <th>Total</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankingProductos as $producto)
                    <tr>
                        <td>{{ strtoupper($producto['nombre_producto']) }}</td>
                        <td>{{ $producto['tradicional'] }}</td>
                        <td>{{ $producto['led'] }}</td>
                        <td>{{ $producto['total'] }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verGruposAnunciantesClientes({{ $producto['id'] }},'productos')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th id="totalTradicional"></th>
                    <th id="totalLed"></th>
                    <th id="totalGeneral"></th>
                    <th ></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
    var table2 = setupDataTable('datatable2');

    function setupDataTable(selector) {
        var table = $('.' + selector).DataTable({
            paging: false,
            order: [[3, 'desc']], // Ordenar por la columna de "Total"
            dom: 'Brtp <"actions">',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: true,
            scrollY: "25vh",
            scrollCollapse: true,
            buttons: [
                'excel'
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Convierte los valores a enteros y realiza la suma
                var totalTradicional = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);

                var totalLED = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);

                var totalTotal = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);

                // Muestra los totales en el pie de página sin decimales
                $(api.column(1).footer()).html(totalTradicional);
                $(api.column(2).footer()).html(totalLED);
                $(api.column(3).footer()).html(totalTotal);
            }
        });

        //table.buttons().remove();
        addSearchInput(table, selector);

        return table;
    }

    function addSearchInput(table, selector) {
        $(selector + ' thead tr:eq(0) th').each(function(i) {
            $(this).append(
                '<input type="text" placeholder="Buscar: " style="width:100%; font-size:11px; line-height:15px; border:none; border:1px solid #f2f2f2;" />'
            );
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table.column(i).search(this.value).draw();
                }
            });
        });
    }

    function verGruposAnunciantesClientes(id,grafico){
        // Seleccionar el option correspondiente en el selector de empresa
        const graficoSelect = document.getElementById('grafico');
        // Establecer el valor seleccionado para grafico
        graficoSelect.value = grafico;
        var unidad = $('#unidad').val();
        var periodo = $('#periodo').val();
        var empresa = 0;
        var uso = $('#uso').val();
        var grafico = grafico;


        //validamos que todos los select se encuentren seleccionados es decir sean diferente a ''
        if (unidad && periodo && grafico) {
            // Realizar una solicitud AJAX para obtener el contenido HTML de la vista Blade
            $.ajax({
                url: '/estudio-competencia/graficos/' + grafico,
                type: 'GET',
                data: {
                    unidad: unidad,
                    periodo: periodo,
                    empresa: empresa,
                    uso: uso,
                    id: id
                },
                success: function(response) {
                    // Cargar el contenido en el div#contenido
                    $('#contenido').html(response);
                    //lanzar el contador
                    var options = {
                        delay: 10,   // Tiempo entre cada incremento del contador
                        time: 1000   // Duración total del conteo
                    };
                    initializeCounterUp('.counter', options);

                },
                error: function(xhr) {
                    console.error('Error al cargar el gráfico:', xhr.responseText);
                }
            });
        }

    }
</script>
