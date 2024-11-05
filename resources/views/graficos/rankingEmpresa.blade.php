<div class="row">
    <div class="col-6">
        <div class="text-dos mt-1" style="margin-bottom:-24px;">Ranking Anunciantes por empresa</div>
        <table class="datatable2 table table-striped w-100">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Total tradicional</th>
                    <th>Total led</th>
                    <th>Total anunciantes</th>
                    <th style="width: 12%;">Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $value)
                    @php
                        $clase = '';
                        if($value->empresa=='Marketmedios'){
                            $clase = 'text-danger';
                        }
                    @endphp
                    <tr>
                        <td class="{{$clase}}">{{ strtoupper($value->empresa) }}</td>
                        <td>{{ $value->anunciantes_tradicional }}</td>
                        <td>{{ $value->anunciantes_led }}</td>
                        <td>{{ $value->anunciantes }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verAnunciantesClientes({{ $value->empresa_id }},'anunciantes')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th></th>
                    <th></th>
                    <th id="total" class="text-start"></th>
                    <th ></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-6  ">
        <div class="text-dos mt-1 " style="margin-bottom:-24px;">Ranking Clientes por empresa</div>
        <table class="datatable2 table table-striped w-100" >
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Tradicional</th>
                    <th>Led</th>
                    <th>Clientes</th>
                    <th>Clientes directos</th>
                    <th style="width: 8%;">Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $value)
                     @php
                        $clase = '';
                        if($value->empresa=='Marketmedios'){
                            $clase = 'text-danger';
                        }
                    @endphp
                    <tr>
                        <td class="{{$clase}}">{{ strtoupper($value->empresa) }}</td>
                        <td>{{ $value->clientes_tradicional }}</td>
                        <td>{{ $value->clientes_led }}</td>
                        <td>{{ $value->clientes }}</td>
                        <td>{{ $value->clientes_directo }}</td>
                        <td>
                            <i class="fa-solid fa-eye" style="font-size:14px;" onclick="verAnunciantesClientes({{ $value->empresa_id }},'clientes')"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th></th>
                    <th></th>
                    <th id="total" class="text-start"></th>
                    <th class="text-start"></th>
                    <th></th>
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
            order: [[1, 'desc']], // Ordenar por la columna de "Total"
            dom: 'Brtp <"actions">',
            orderCellsTop: true,
            fixedHeader: true,
            scrollX: true,
            scrollY: "56vh",
            scrollCollapse: true,
            buttons: [
                'excel'
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                // Convierte los valores a enteros y realiza la suma
                var total4 = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);
                // Muestra los totales en el pie de página sin decimales
                $(api.column(1).footer()).html(total4);
                var total3 = api.column(2, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);
                // Muestra los totales en el pie de página sin decimales
                $(api.column(2).footer()).html(total3);
                var total = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);
                // Muestra los totales en el pie de página sin decimales
                $(api.column(3).footer()).html(total);

                var total2 = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);
                // Muestra los totales en el pie de página sin decimales
                $(api.column(4).footer()).html(total2);
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

    function verAnunciantesClientes(empresa,grafico){
        // Seleccionar el option correspondiente en el selector de empresa
        const empresaSelect = document.getElementById('empresa');
        const graficoSelect = document.getElementById('grafico');
        // Establecer el valor seleccionado para empresa
        empresaSelect.value = empresa;
        // Establecer el valor seleccionado para grafico
        graficoSelect.value = grafico;
        var unidad = $('#unidad').val();
        var periodo = $('#periodo').val();
        var empresa = empresa;
        var uso = $('#uso').val();
        var grafico = grafico;

        //validamos que todos los select se encuentren seleccionados es decir sean diferente a ''
        if (unidad && periodo && empresa && grafico) {
            // Realizar una solicitud AJAX para obtener el contenido HTML de la vista Blade
            $.ajax({
                url: '/estudio-competencia/graficos/' + grafico,
                type: 'GET',
                data: {
                    unidad: unidad,
                    periodo: periodo,
                    empresa: empresa,
                    uso: uso
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
