<table class="datatable2 table table-striped w-100" style="font-size: 12px;">
    <thead>
        <tr>
            <th>Periodo</th>
            <th>Caras activas</th>
            <th>Tradicionales</th>
            <th>Led</th>
            <th>Desactivadas</th>
            <th>Activadas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($resultadosPorMes as $value)
            @if($value['mes']>0)
                <tr>
                    <td>{{ ($value['mes']<10?'0'.$value['mes']:$value['mes']) }} - {{ $value['anno'] }}</td>
                    <td>{{ $value['lugaresActivos'] }}</td>
                    <td>{{ $value['carasTradicional'] }}</td>
                    <td>{{ $value['carasLed'] }}</td>
                    <td>{{ $value['desactivados'] }}</td>
                    <td>{{ $value['activados'] }}</td>
                </tr>
            @endif
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