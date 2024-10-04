<table class="datatable2 table table-striped w-100" style="font-size: 12px;">
    <thead>
        <tr>
            <th>Propietario</th>
            <th>Reporte</th>
            <th>Inventario</th>
            <th>Ubicaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($resultado as $key => $value)
            <tr>
                @if (array_key_exists($key, $propietarios))
                    <td>{{ $propietarios[$key]->descripcion }}</td>
                @else
                    <td>{{ $key }}</td> 
                @endif
                <td>{{ isset($value['reporte']) ? $value['reporte'] : 0 }}</td>
                <td>{{ $value['inventario'] }}</td>
                <td>{{ isset($value['ubicaciones']) ? implode(', ', $value['ubicaciones']) : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    var table2 = setupDataTable('datatable2');

    function setupDataTable(selector) {
        var table = $('.' + selector).DataTable({
            paging: false,
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
                var total = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseInt(a) + parseInt(b); // Convertir los valores a enteros antes de sumar
                }, 0);
                // Muestra los totales en el pie de página sin decimales
                $(api.column(1).footer()).html(total);
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
</script>